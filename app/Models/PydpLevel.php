<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PydpLevel extends Model
{
    use HasFactory;

    protected $table = 'pydp_levels';

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'order',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============ RELATIONSHIPS ============

    /**
     * Get the user who manages/passes this level
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all indicators at this level
     */
    public function indicators(): HasMany
    {
        return $this->hasMany(PydpIndicator::class, 'pydp_level_id');
    }

    // ============ SCOPES ============

    /**
     * Get levels ordered by sequence
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get levels with indicators
     */
    public function scopeWithIndicators($query)
    {
        return $query->with('indicators');
    }

    /**
     * Get levels with user data
     */
    public function scopeWithUser($query)
    {
        return $query->with('user.userData');
    }

    /**
     * Search by title
     */
    public function scopeSearchByTitle($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    /**
     * Filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ============ ACCESSORS ============

    /**
     * Get approver name
     */
    public function getApproverNameAttribute(): string
    {
        if (!$this->user || !$this->user->userData) {
            return 'N/A';
        }

        return trim(
            ($this->user->userData->first_name ?? '') . ' ' .
            ($this->user->userData->middle_name ?? '') . ' ' .
            ($this->user->userData->last_name ?? '') . ' ' .
            ($this->user->userData->name_extension ?? '')
        ) ?: 'Unknown';
    }

    /**
     * Get approver agency
     */
    public function getApproverAgencyAttribute(): string
    {
        return $this->user?->userData?->government_agency ?? 'N/A';
    }

    /**
     * Get approver position
     */
    public function getApproverPositionAttribute(): string
    {
        return $this->user?->userData?->position_designation ?? 'N/A';
    }

    /**
     * Get full approver info
     */
    public function getApproverInfoAttribute(): array
    {
        return [
            'name' => $this->approver_name,
            'agency' => $this->approver_agency,
            'position' => $this->approver_position,
        ];
    }

    // ============ METHODS ============

    /**
     * Get all submitted entries from all indicators in this level
     */
    public function getSubmittedEntries()
    {
        return PydpDatasetEntry::whereIn('pydp_indicator_id', $this->indicators()->pluck('id'))
                               ->where('submission_status', 'submitted')
                               ->where('edit_requested', false)
                               ->get();
    }

    /**
     * Get all approved entries from all indicators in this level
     */
    public function getApprovedEntries()
    {
        return PydpDatasetEntry::whereIn('pydp_indicator_id', $this->indicators()->pluck('id'))
                               ->where('submission_status', 'approved')
                               ->get();
    }

    /**
     * Get all rejected entries from all indicators in this level
     */
    public function getRejectedEntries()
    {
        return PydpDatasetEntry::whereIn('pydp_indicator_id', $this->indicators()->pluck('id'))
                               ->where('submission_status', 'rejected')
                               ->get();
    }

    /**
     * Count total indicators in this level
     */
    public function getIndicatorCount(): int
    {
        return $this->indicators()->count();
    }

    /**
     * Count total entries across all indicators
     */
    public function getEntryCount(): int
    {
        return PydpDatasetEntry::whereIn('pydp_indicator_id', $this->indicators()->pluck('id'))->count();
    }

    /**
     * Count submitted entries
     */
    public function getSubmittedCount(): int
    {
        return $this->getSubmittedEntries()->count();
    }

    /**
     * Count approved entries
     */
    public function getApprovedCount(): int
    {
        return $this->getApprovedEntries()->count();
    }

    /**
     * Count rejected entries
     */
    public function getRejectedCount(): int
    {
        return $this->getRejectedEntries()->count();
    }

    /**
     * Get submission status of this level
     */
    public function getStatusAttribute(): string
    {
        if (!$this->indicators()->exists()) {
            return 'draft';
        }

        $entries = PydpDatasetEntry::whereIn('pydp_indicator_id', $this->indicators()->pluck('id'))->get();

        if ($entries->isEmpty()) {
            return 'draft';
        }

        $statuses = $entries->pluck('submission_status')->unique();

        if ($statuses->count() === 1) {
            return $statuses->first();
        }

        return 'mixed';
    }

    /**
     * Check if all indicators have submitted entries
     */
    public function isFullySubmitted(): bool
    {
        return $this->getSubmittedCount() > 0 && $this->indicators()->count() > 0;
    }

    /**
     * Check if all indicators have approved entries
     */
    public function isFullyApproved(): bool
    {
        $totalEntries = $this->getEntryCount();
        $approvedEntries = $this->getApprovedCount();
        
        return $totalEntries > 0 && $totalEntries === $approvedEntries;
    }
}