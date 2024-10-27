<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Positions extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'committee_id',
        'position',
    ];

    public function committees(){
        return $this->belongsTo(Committees::class);
    }
}
