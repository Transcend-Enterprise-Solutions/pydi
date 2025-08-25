<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PydpIndicator extends Model
{
    use HasFactory;
    protected $table = 'pydp_indicators';
    protected $guarded = [];

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
}
