<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PydpDatasetDetail extends Model
{
    use HasFactory;
    protected $table = 'pydp_dataset_details';
    protected $guarded = [];

    public function pydpDataset()
    {
        return $this->belongsTo(PydiDataset::class, 'pydp_dataset_id');
    }

    public function indicator()
    {
        return $this->belongsTo(PydpIndicator::class, 'pydp_indicator_id');
    }

    public function dimension()
    {
        return $this->belongsTo(Dimension::class, 'dimension_id');
    }

    public function years()
    {
        return $this->hasMany(PydpYear::class, 'pydp_dataset_detail_id');
    }
}
