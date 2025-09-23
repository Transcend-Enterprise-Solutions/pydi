<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PydpLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        // Add other fillable fields as needed
    ];

    /**
     * Get the user that owns the PYDP level.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the indicators for the PYDP level.
     */
    public function indicators()
    {
        return $this->hasMany(PydpIndicator::class, 'pydp_level_id');
    }
}
