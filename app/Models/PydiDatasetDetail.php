<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PydiDatasetDetail extends Model
{
    use HasFactory;
    protected $table = 'pydi_dataset_details';
    protected $guarded = [];

    public function pydiDataset()
    {
        return $this->belongsTo(PydiDataset::class, 'pydi_dataset_id');
    }

    public function dimension()
    {
        return $this->belongsTo(Dimension::class, 'dimension_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function region()
    {
        return $this->belongsTo(PhilippineRegions::class, 'philippine_region_id');
    }
}
