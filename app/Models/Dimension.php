<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dimension extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all indicators for this dimension.
     */
    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class);
    }

    /**
     * Get all PYDI data records for this dimension.
     */
    public function dataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class);
    }

    /**
     * Get approved data records for this dimension.
     */
    public function approvedDataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class)->approved();
    }

    /**
     * Get submitted data records for this dimension.
     */
    public function submittedDataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class)->submitted();
    }

    /**
     * Get draft data records for this dimension.
     */
    public function draftDataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class)->draft();
    }

    /**
     * Get count of indicators for this dimension.
     */
    public function getIndicatorsCountAttribute(): int
    {
        return $this->indicators()->count();
    }

    /**
     * Get count of data records for this dimension.
     */
    public function getDataRecordsCountAttribute(): int
    {
        return $this->dataRecords()->count();
    }

    /**
     * Get count of approved data records for this dimension.
     */
    public function getApprovedRecordsCountAttribute(): int
    {
        return $this->approvedDataRecords()->count();
    }

    /**
     * Check if dimension has indicators.
     */
    public function hasIndicators(): bool
    {
        return $this->indicators()->exists();
    }

    /**
     * Check if dimension has data records.
     */
    public function hasDataRecords(): bool
    {
        return $this->dataRecords()->exists();
    }

    /**
     * Scope for dimensions with indicators.
     */
    public function scopeWithIndicators($query)
    {
        return $query->has('indicators');
    }

    /**
     * Scope for dimensions with data records.
     */
    public function scopeWithDataRecords($query)
    {
        return $query->has('dataRecords');
    }

    public function pydiDatasetDetals()
    {
        return $this->hasMany(PydiDatasetDetail::class, 'dimension_id', 'id');
    }
}
