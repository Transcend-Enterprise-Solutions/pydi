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
}
