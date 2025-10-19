<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'dimension_id',
        'name',
        'description',
        'measurement_unit',
    ];

    /**
     * Get the dimension that owns this indicator.
     */
    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class, 'dimension_id', 'id');
    }

    /**
     * Get all PYDI datasets for this indicator.
     */
    public function pydiDatasets(): HasMany
    {
        return $this->hasMany(PydiDataset::class, 'indicator_id', 'id');
    }

    /**
     * Get all PYDI data records for this indicator.
     */
    public function dataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class, 'indicator_id', 'id');
    }

    /**
     * Get all PYDI dataset details for this indicator.
     */
    public function pydiDatasetDetails(): HasMany
    {
        return $this->hasMany(PydiDatasetDetail::class, 'indicator_id', 'id');
    }

    /**
     * Get approved data records for this indicator.
     */
    public function approvedDataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class, 'indicator_id', 'id')->where('status', 'approved');
    }

    /**
     * Get submitted data records for this indicator.
     */
    public function submittedDataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class, 'indicator_id', 'id')->where('is_submitted', true);
    }

    /**
     * Get draft data records for this indicator.
     */
    public function draftDataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class, 'indicator_id', 'id')->where('is_submitted', false);
    }

    /**
     * Get count of data records for this indicator.
     */
    public function getDataRecordsCountAttribute(): int
    {
        return $this->dataRecords()->count();
    }

    /**
     * Get count of approved data records for this indicator.
     */
    public function getApprovedRecordsCountAttribute(): int
    {
        return $this->approvedDataRecords()->count();
    }

    /**
     * Get the full name with dimension.
     */
    public function getFullNameAttribute(): string
    {
        return $this->dimension ? $this->dimension->name . ' - ' . $this->name : $this->name;
    }

    /**
     * Get formatted measurement unit.
     */
    public function getFormattedUnitAttribute(): string
    {
        return $this->measurement_unit ? '(' . $this->measurement_unit . ')' : '';
    }

    /**
     * Check if indicator has data records.
     */
    public function hasDataRecords(): bool
    {
        return $this->dataRecords()->exists();
    }

    /**
     * Get average value for this indicator.
     */
    public function getAverageValue(): float
    {
        return $this->approvedDataRecords()->avg('value') ?? 0;
    }

    /**
     * Get latest value for this indicator.
     */
    public function getLatestValue(): ?float
    {
        return $this->approvedDataRecords()->latest()->value('value');
    }

    /**
     * Get data records by region.
     */
    public function getDataByRegion(string $region)
    {
        return $this->approvedDataRecords()->where('region', $region)->get();
    }

    /**
     * Get data records by sex.
     */
    public function getDataBySex(string $sex)
    {
        return $this->approvedDataRecords()->where('sex', $sex)->get();
    }

    /**
     * Get data records by age group.
     */
    public function getDataByAge(string $age)
    {
        return $this->approvedDataRecords()->where('age', $age)->get();
    }

    /**
     * Scope for indicators with data records.
     */
    public function scopeWithDataRecords($query)
    {
        return $query->has('dataRecords');
    }

    /**
     * Scope for indicators by dimension.
     */
    public function scopeForDimension($query, $dimensionId)
    {
        return $query->where('dimension_id', $dimensionId);
    }
}