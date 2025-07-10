<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PydiDataRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_session_id',
        'dimension_id',
        'indicator_id',
        'user_id',
        'region',
        'sex',
        'age',
        'value',
        'remarks',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the upload session that owns this record.
     */
    public function uploadSession(): BelongsTo
    {
        return $this->belongsTo(UploadSession::class);
    }

    /**
     * Get the dimension for this record.
     */
    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class);
    }

    /**
     * Get the indicator for this record.
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    /**
     * Get the user who created this record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved this record.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if record is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if record is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if record is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if record is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Mark record as submitted.
     */
    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Mark record as approved.
     */
    public function markAsApproved($approvedBy = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Mark record as rejected.
     */
    public function markAsRejected(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Get formatted value with indicator unit.
     */
    public function getFormattedValueAttribute(): string
    {
        $unit = $this->indicator->measurement_unit ?? '';
        return number_format($this->value, 2) . ($unit ? ' ' . $unit : '');
    }

    /**
     * Scope for draft records.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for submitted records.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope for approved records.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected records.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for records by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for records by dimension.
     */
    public function scopeForDimension($query, $dimensionId)
    {
        return $query->where('dimension_id', $dimensionId);
    }

    /**
     * Scope for records by indicator.
     */
    public function scopeForIndicator($query, $indicatorId)
    {
        return $query->where('indicator_id', $indicatorId);
    }

    /**
     * Scope for records by region.
     */
    public function scopeForRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope for records by sex.
     */
    public function scopeForSex($query, $sex)
    {
        return $query->where('sex', $sex);
    }

    /**
     * Scope for records by age group.
     */
    public function scopeForAge($query, $age)
    {
        return $query->where('age', $age);
    }
}
