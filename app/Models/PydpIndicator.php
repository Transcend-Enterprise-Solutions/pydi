<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PydpIndicator extends Model
{
    use HasFactory;

    protected $table = 'pydp_indicators';

    protected $fillable = [
        'pydp_level_id',
        'title',
        'content',
        'data_sources',
        'frequency',
        'responsible',
        'validation',
        'data_sharing',
        'measurement_unit',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the level this indicator belongs to
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(PydpLevel::class, 'pydp_level_id');
    }

    /**
     * Get all entries for this indicator
     */
    public function entries(): HasMany
    {
        return $this->hasMany(PydpDatasetEntry::class, 'pydp_indicator_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope to include entries with specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->whereHas('entries', function ($q) use ($status) {
            $q->where('submission_status', $status);
        });
    }

    /**
     * Scope to search by title
     */
    public function scopeSearchByTitle($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by level
     */
    public function scopeByLevel($query, $levelId)
    {
        return $query->where('pydp_level_id', $levelId);
    }

    /**
     * Scope to include level with user data
     */
    public function scopeWithLevelAndUser($query)
    {
        return $query->with(['level', 'level.user.userData']);
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Get entries by status
     */
    public function getEntriesByStatus($status)
    {
        return $this->entries()
                    ->where('submission_status', $status)
                    ->get();
    }

    /**
     * Get submitted entries
     */
    public function getSubmittedEntries()
    {
        return $this->entries()
                    ->where('submission_status', 'submitted')
                    ->where('edit_requested', false)
                    ->orderBy('year', 'desc')
                    ->get();
    }

    /**
     * Get approved entries
     */
    public function getApprovedEntries()
    {
        return $this->entries()
                    ->where('submission_status', 'approved')
                    ->orderBy('year', 'desc')
                    ->get();
    }

    /**
     * Get rejected entries
     */
    public function getRejectedEntries()
    {
        return $this->entries()
                    ->where('submission_status', 'rejected')
                    ->orderBy('year', 'desc')
                    ->get();
    }

    /**
     * Get entries for a specific year
     */
    public function getEntriesByYear($year)
    {
        return $this->entries()
                    ->where('year', $year)
                    ->get();
    }

    /**
     * Get entries by year range
     */
    public function getEntriesByYearRange($startYear, $endYear)
    {
        return $this->entries()
                    ->whereBetween('year', [$startYear, $endYear])
                    ->orderBy('year', 'desc')
                    ->get();
    }

    /**
     * Get entry for specific year (first or create)
     */
    public function getOrCreateEntryForYear($year)
    {
        return $this->entries()
                    ->where('year', $year)
                    ->firstOrCreate([
                        'year' => $year,
                        'submission_status' => 'draft',
                        'edit_requested' => false,
                    ]);
    }

    /**
     * Count total entries
     */
    public function getEntryCount(): int
    {
        return $this->entries()->count();
    }

    /**
     * Count submitted entries
     */
    public function getSubmittedCount(): int
    {
        return $this->entries()
                    ->where('submission_status', 'submitted')
                    ->where('edit_requested', false)
                    ->count();
    }

    /**
     * Count approved entries
     */
    public function getApprovedCount(): int
    {
        return $this->entries()
                    ->where('submission_status', 'approved')
                    ->count();
    }

    /**
     * Count rejected entries
     */
    public function getRejectedCount(): int
    {
        return $this->entries()
                    ->where('submission_status', 'rejected')
                    ->count();
    }

    /**
     * Get status statistics
     */
    public function getStatusStats(): array
    {
        return [
            'draft' => $this->entries()->where('submission_status', 'draft')->count(),
            'submitted' => $this->getSubmittedCount(),
            'approved' => $this->getApprovedCount(),
            'rejected' => $this->getRejectedCount(),
            'total' => $this->getEntryCount(),
        ];
    }

    /**
     * Check if indicator has any submitted entries
     */
    public function hasSubmittedEntries(): bool
    {
        return $this->getSubmittedCount() > 0;
    }

    /**
     * Check if indicator has any approved entries
     */
    public function hasApprovedEntries(): bool
    {
        return $this->getApprovedCount() > 0;
    }

    /**
     * Check if indicator has any rejected entries
     */
    public function hasRejectedEntries(): bool
    {
        return $this->getRejectedCount() > 0;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage(): int
    {
        $total = $this->getEntryCount();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->entries()
                         ->where('submission_status', '!=', 'draft')
                         ->count();

        return (int)(($completed / $total) * 100);
    }

    /**
     * Get approval percentage
     */
    public function getApprovalPercentage(): int
    {
        $total = $this->getEntryCount();
        if ($total === 0) {
            return 0;
        }

        $approved = $this->getApprovedCount();

        return (int)(($approved / $total) * 100);
    }
}