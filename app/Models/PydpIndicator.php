<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PydpIndicator extends Model
{
    use HasFactory;
    
    protected $table = 'pydp_indicators';
    protected $guarded = [];

    protected $fillable = [
        'pydp_level_id',
        'pydp_type_id',
        'title',
        'content',
        'data_sources',
        'frequency',
        'responsible',
        'validation',
        'data_sharing',
        'measurement_unit',
    ];

    public function type()
    {
        return $this->belongsTo(PydpType::class, 'pydp_type_id');
    }

    public function level()
    {
        return $this->belongsTo(PydpLevel::class, 'pydp_level_id');
    }

    public function datasetDetails()
    {
        return $this->hasMany(PydpDatasetDetail::class, 'pydp_indicator_id');
    }

    public function entries()
    {
        return $this->hasMany(PydpDatasetEntry::class, 'pydp_indicator_id')->orderBy('year');
    }

    public function getDetails(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->content,
            'data_sources' => $this->data_sources,
            'frequency' => $this->frequency,
            'responsible' => $this->responsible,
            'validation' => $this->validation,
            'data_sharing' => $this->data_sharing,
            'measurement_unit' => $this->measurement_unit,
            'type' => $this->type?->name,
            'level' => $this->level?->title,
        ];
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('level', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function getFormattedDetails(): array
    {
        return [
            'Indicator' => $this->title,
            'Description' => $this->content,
            'Data Sources' => $this->data_sources,
            'Frequency of Data Collection' => $this->frequency,
            'Persons/Units Responsible' => $this->responsible,
            'Validation and Reporting Mechanisms' => $this->validation,
            'Data Sharing and Utilization' => $this->data_sharing,
            'Measurement Unit' => $this->measurement_unit,
        ];
    }

    public function hasCompleteDetails(): bool
    {
        return !empty($this->title) && !empty($this->content);
    }

    public function hasExtendedInfo(): bool
    {
        return !empty($this->data_sources) ||
               !empty($this->frequency) ||
               !empty($this->responsible) ||
               !empty($this->validation) ||
               !empty($this->data_sharing);
    }
}