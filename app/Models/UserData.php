<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserData extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_data';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'tel_number',
        'mobile_number',
        'block',
        'lot',
        'street',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
