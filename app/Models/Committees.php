<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committees extends Model
{
    use HasFactory;

    protected $table = 'committees';

    protected $fillable = [
        'committee',
    ];

    public function positions(){
        return $this->hasMany(Positions::class, 'committee_id');
    }

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('committees.committee', 'like', $term);
        });
    }
}
