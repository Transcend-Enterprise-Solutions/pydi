<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PydiDataset extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'pydi_datasets';
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(PydiDatasetDetail::class, 'pydi_dataset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type()
    {
        return $this->belongsTo(PydpType::class, 'pydp_type_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
