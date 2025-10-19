<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PydiDataset extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'pydi_datasets';
    protected $guarded = [];

    protected $casts = [
        'is_submitted' => 'boolean',
        'is_request_edit' => 'boolean',
        'submitted_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    /**
     * Get all dataset details for this dataset.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PydiDatasetDetail::class, 'pydi_dataset_id');
    }

    /**
     * Get the user that owns this dataset.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the type of this dataset.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PydpType::class, 'pydp_type_id');
    }

    /**
     * Get the reviewer assigned to this dataset.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the indicator associated with this dataset.
     * NEW RELATIONSHIP for indicator dropdown feature
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    /**
     * Check if dataset is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if dataset is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if dataset is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if dataset needs revision.
     */
    public function needsRevision(): bool
    {
        return $this->status === 'needs_revision';
    }

    /**
     * Check if dataset has been submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->is_submitted === true;
    }

    /**
     * Check if edit request is pending.
     */
    public function hasEditRequest(): bool
    {
        return $this->is_request_edit === true;
    }

    /**
     * Get the dimension through the indicator relationship.
     */
    public function getDimensionAttribute()
    {
        return $this->indicator?->dimension;
    }

    /**
     * Get the full indicator name with dimension.
     */
    public function getFullIndicatorNameAttribute(): string
    {
        if ($this->indicator && $this->indicator->dimension) {
            return $this->indicator->dimension->name . ' - ' . $this->indicator->name;
        }
        return $this->name ?? 'N/A';
    }

    /**
     * Scope for approved datasets.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending datasets.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for rejected datasets.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for submitted datasets.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    /**
     * Scope for datasets with edit requests.
     */
    public function scopeWithEditRequest($query)
    {
        return $query->where('is_request_edit', true);
    }

    /**
     * Scope for datasets by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for datasets by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Get count of dataset details.
     */
    public function getDetailsCountAttribute(): int
    {
        return $this->details()->count();
    }

    /**
     * Check if dataset has any details.
     */
    public function hasDetails(): bool
    {
        return $this->details()->exists();
    }
}