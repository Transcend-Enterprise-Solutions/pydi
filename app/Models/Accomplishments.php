<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accomplishments extends Model
{
    use HasFactory;

    protected $fillable = [
        'indicator_id',
        'ppa_name',
        'year',
        'target_physical',
        'target_financial',
        'actual_physical',
        'actual_financial',
        'status',
        'admin_feedback',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'year' => 'integer',
        'target_physical' => 'decimal:2',
        'target_financial' => 'decimal:2',
        'actual_physical' => 'decimal:2',
        'actual_financial' => 'decimal:2',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'needs_revision' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Calculate accomplishment rate for physical targets
    public function getPhysicalAccomplishmentRateAttribute()
    {
        if ($this->target_physical && $this->target_physical > 0) {
            return ($this->actual_physical / $this->target_physical) * 100;
        }
        return null;
    }

    // Calculate accomplishment rate for financial targets
    public function getFinancialAccomplishmentRateAttribute()
    {
        if ($this->target_financial && $this->target_financial > 0) {
            return ($this->actual_financial / $this->target_financial) * 100;
        }
        return null;
    }
}