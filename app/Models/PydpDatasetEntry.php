<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PydpDatasetEntry extends Model
{
    use HasFactory;

    protected $table = 'pydp_dataset_entries';

    protected $fillable = [
        'pydp_indicator_id',
        'year',
        
        // Data fields
        'baseline',
        'physical_target_male',
        'physical_target_female',
        'physical_target_total',
        'physical_actual_male',
        'physical_actual_female',
        'physical_actual_total',
        'financial_allotted',
        'financial_spent',
        'remarks',
        
        // Submission fields
        'submission_status',
        'submitted_at',
        'submitted_by',
        'admin_notes',
        
        // Notes & Edit Request fields
        'submission_notes',
        'edit_requested',
        'edit_request_reason',
        'edit_requested_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'submitted_at' => 'datetime',
        'edit_requested_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'edit_requested' => 'boolean',
    ];

    protected $dates = [
        'submitted_at',
        'edit_requested_at',
        'created_at',
        'updated_at',
    ];

    // ============ RELATIONSHIPS ============

    /**
     * Get the indicator this entry belongs to
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(PydpIndicator::class, 'pydp_indicator_id');
    }

    /**
     * Get the user who submitted this entry
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // ============ STATUS CHECKERS ============

    /**
     * Check if entry is editable (draft or edit requested)
     */
    public function isEditable(): bool
    {
        return in_array($this->submission_status, ['draft', 'edit_requested']);
    }

    /**
     * Check if entry is in draft status
     */
    public function isDraft(): bool
    {
        return $this->submission_status === 'draft';
    }

    /**
     * Check if entry is submitted
     */
    public function isSubmitted(): bool
    {
        return $this->submission_status === 'submitted';
    }

    /**
     * Check if entry is approved
     */
    public function isApproved(): bool
    {
        return $this->submission_status === 'approved';
    }

    /**
     * Check if entry is rejected
     */
    public function isRejected(): bool
    {
        return $this->submission_status === 'rejected';
    }

    /**
     * Check if edit is requested
     */
    public function hasEditRequest(): bool
    {
        return $this->edit_requested === true;
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->submission_status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'edit_requested' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get status badge text
     */
    public function getStatusText(): string
    {
        return match($this->submission_status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'edit_requested' => 'Edit Requested',
            default => 'Unknown'
        };
    }

    /**
     * Get formatted submission date
     */
    public function getFormattedSubmissionDate(): string
    {
        return $this->submitted_at?->format('M d, Y H:i') ?? 'Not submitted';
    }

    /**
     * Get formatted edit request date
     */
    public function getFormattedEditRequestDate(): string
    {
        return $this->edit_requested_at?->format('M d, Y H:i') ?? 'Never requested';
    }

    /**
     * Check if has all required data fields filled
     */
    public function hasAllRequiredFields(): bool
    {
        return !empty($this->baseline) && 
               !empty($this->physical_target_total) && 
               !empty($this->physical_actual_total) && 
               !empty($this->financial_allotted);
    }

    /**
     * Get percentage of data fields filled
     */
    public function getDataCompletionPercentage(): int
    {
        $fields = [
            'baseline',
            'physical_target_male',
            'physical_target_female',
            'physical_target_total',
            'physical_actual_male',
            'physical_actual_female',
            'physical_actual_total',
            'financial_allotted',
            'financial_spent',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        return (int)(($filled / count($fields)) * 100);
    }

    // ============ SCOPES ============

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('submission_status', $status);
    }

    /**
     * Scope: Get draft entries
     */
    public function scopeDraft($query)
    {
        return $query->where('submission_status', 'draft');
    }

    /**
     * Scope: Get submitted entries
     */
    public function scopeSubmitted($query)
    {
        return $query->where('submission_status', 'submitted');
    }

    /**
     * Scope: Get approved entries
     */
    public function scopeApproved($query)
    {
        return $query->where('submission_status', 'approved');
    }

    /**
     * Scope: Get rejected entries
     */
    public function scopeRejected($query)
    {
        return $query->where('submission_status', 'rejected');
    }

    /**
     * Scope: Get entries with edit request
     */
    public function scopeEditRequested($query)
    {
        return $query->where('edit_requested', true);
    }

    /**
     * Scope: Get entries by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope: Get entries by year range
     */
    public function scopeByYearRange($query, $startYear, $endYear)
    {
        return $query->whereBetween('year', [$startYear, $endYear]);
    }

    /**
     * Scope: Get entries by indicator
     */
    public function scopeByIndicator($query, $indicatorId)
    {
        return $query->where('pydp_indicator_id', $indicatorId);
    }

    /**
     * Scope: Get entries submitted after date
     */
    public function scopeSubmittedAfter($query, $date)
    {
        return $query->where('submitted_at', '>=', $date);
    }

    /**
     * Scope: Get entries submitted before date
     */
    public function scopeSubmittedBefore($query, $date)
    {
        return $query->where('submitted_at', '<=', $date);
    }

    /**
     * Scope: Get incomplete entries (missing data)
     */
    public function scopeIncomplete($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('baseline')
              ->orWhereNull('physical_target_total')
              ->orWhereNull('physical_actual_total');
        });
    }

    /**
     * Scope: Get complete entries (all data filled)
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('baseline')
                     ->whereNotNull('physical_target_total')
                     ->whereNotNull('physical_actual_total');
    }

    /**
     * Scope: Get entries pending approval (submitted but not approved)
     */
    public function scopePendingApproval($query)
    {
        return $query->where('submission_status', 'submitted')
                     ->where('edit_requested', false);
    }

    /**
     * Scope: Get entries needing action (edit requested or rejected)
     */
    public function scopeNeedingAction($query)
    {
        return $query->where(function ($q) {
            $q->where('edit_requested', true)
              ->orWhere('submission_status', 'rejected');
        });
    }

    // ============ MUTATORS ============

    /**
     * Ensure numeric fields are stored as numbers
     */
    public function setBaselineAttribute($value)
    {
        $this->attributes['baseline'] = empty($value) ? null : $value;
    }

    public function setPhysicalTargetMaleAttribute($value)
    {
        $this->attributes['physical_target_male'] = empty($value) ? null : $value;
    }

    public function setPhysicalTargetFemaleAttribute($value)
    {
        $this->attributes['physical_target_female'] = empty($value) ? null : $value;
    }

    public function setPhysicalTargetTotalAttribute($value)
    {
        $this->attributes['physical_target_total'] = empty($value) ? null : $value;
    }

    public function setPhysicalActualMaleAttribute($value)
    {
        $this->attributes['physical_actual_male'] = empty($value) ? null : $value;
    }

    public function setPhysicalActualFemaleAttribute($value)
    {
        $this->attributes['physical_actual_female'] = empty($value) ? null : $value;
    }

    public function setPhysicalActualTotalAttribute($value)
    {
        $this->attributes['physical_actual_total'] = empty($value) ? null : $value;
    }

    public function setFinancialAllottedAttribute($value)
    {
        $this->attributes['financial_allotted'] = empty($value) ? null : $value;
    }

    public function setFinancialSpentAttribute($value)
    {
        $this->attributes['financial_spent'] = empty($value) ? null : $value;
    }

    // ============ ACTIONS ============

    /**
     * Mark entry as submitted
     */
    public function markAsSubmitted(string $notes = null, int $submittedBy = null): void
    {
        $this->update([
            'submission_status' => 'submitted',
            'submitted_at' => now(),
            'submission_notes' => $notes,
            'submitted_by' => $submittedBy,
            'edit_requested' => false,
            'edit_request_reason' => null,
        ]);
    }

    /**
     * Mark entry as approved
     */
    public function markAsApproved(string $notes = null): void
    {
        $this->update([
            'submission_status' => 'approved',
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Mark entry as rejected with reason
     */
    public function markAsRejected(string $reason): void
    {
        $this->update([
            'submission_status' => 'rejected',
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Request edit with reason
     */
    public function requestEdit(string $reason): void
    {
        $this->update([
            'edit_requested' => true,
            'edit_request_reason' => $reason,
            'edit_requested_at' => now(),
        ]);
    }

    /**
     * Reset to draft status
     */
    public function resetToDraft(): void
    {
        $this->update([
            'submission_status' => 'draft',
            'submitted_at' => null,
            'edit_requested' => false,
            'edit_request_reason' => null,
        ]);
    }

    /**
     * Clear all data fields
     */
    public function clearDataFields(): void
    {
        $this->update([
            'baseline' => null,
            'physical_target_male' => null,
            'physical_target_female' => null,
            'physical_target_total' => null,
            'physical_actual_male' => null,
            'physical_actual_female' => null,
            'physical_actual_total' => null,
            'financial_allotted' => null,
            'financial_spent' => null,
            'remarks' => null,
        ]);
    }

    /**
     * Create a copy of this entry for another year
     */
    public function copyToYear(int $newYear): self
    {
        return self::create([
            'pydp_indicator_id' => $this->pydp_indicator_id,
            'year' => $newYear,
            'baseline' => $this->baseline,
            'physical_target_male' => $this->physical_target_male,
            'physical_target_female' => $this->physical_target_female,
            'physical_target_total' => $this->physical_target_total,
            'physical_actual_male' => $this->physical_actual_male,
            'physical_actual_female' => $this->physical_actual_female,
            'physical_actual_total' => $this->physical_actual_total,
            'financial_allotted' => $this->financial_allotted,
            'financial_spent' => $this->financial_spent,
            'remarks' => $this->remarks,
        ]);
    }
}