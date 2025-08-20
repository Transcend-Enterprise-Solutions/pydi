<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PydpLevel extends Model
{
    use HasFactory;
    protected $table = 'pydp_levels';
    protected $guarded = [];

    public function indicators()
    {
        return $this->hasMany(PydpIndicator::class);
    }
}
