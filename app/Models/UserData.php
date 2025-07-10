<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'mobile_number',
        'position_designation',
        'government_agency',
        'office_department_division',
        'office_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods to get unique values for dropdowns
    public static function getPositionDesignations()
    {
        return self::select('position_designation')
                  ->distinct()
                  ->whereNotNull('position_designation')
                  ->orderBy('position_designation')
                  ->pluck('position_designation');
    }

    public static function getGovernmentAgencies()
    {
        return self::select('government_agency')
                  ->distinct()
                  ->whereNotNull('government_agency')
                  ->orderBy('government_agency')
                  ->pluck('government_agency');
    }

    public static function getOfficeDepartments()
    {
        return self::select('office_department_division')
                  ->distinct()
                  ->whereNotNull('office_department_division')
                  ->orderBy('office_department_division')
                  ->pluck('office_department_division');
    }
}
