<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MonthlyWaterBills extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'monthly_water_bills';
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'status',
        'date_paid',
        'payment_mode',
        'reference_number',
        'received_by',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
