<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'user_data';

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'tel_number',
        'mobile_number',
        'position_designation',
        'government_agency',
        'office_department_division',
        'office_address',
        'block',
        'lot',
        'street',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the user that this data belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get full name with all components
     */
    public function getFullNameAttribute(): string
    {
        $name = trim($this->first_name ?? '');
        
        if ($this->middle_name) {
            $name .= ' ' . trim($this->middle_name);
        }
        
        $name .= ' ' . trim($this->last_name ?? '');
        
        if ($this->name_extension) {
            $name .= ' ' . trim($this->name_extension);
        }
        
        return trim($name);
    }

    /**
     * Get short name (first + last only)
     */
    public function getShortNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope to search by full name
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%")
                     ->orWhere('middle_name', 'like', "%{$search}%");
    }

    /**
     * Scope to search by agency
     */
    public function scopeSearchByAgency($query, $agency)
    {
        return $query->where('government_agency', 'like', "%{$agency}%");
    }

    /**
     * Scope to search by position
     */
    public function scopeSearchByPosition($query, $position)
    {
        return $query->where('position_designation', 'like', "%{$position}%");
    }

    /**
     * Scope to search by department
     */
    public function scopeSearchByDepartment($query, $department)
    {
        return $query->where('office_department_division', 'like', "%{$department}%");
    }

    // ============================================
    // HELPER METHODS - Get unique values for dropdowns
    // ============================================

    /**
     * Get all unique position designations
     */
    public static function getPositionDesignations()
    {
        return self::select('position_designation')
                  ->distinct()
                  ->whereNotNull('position_designation')
                  ->orderBy('position_designation')
                  ->pluck('position_designation');
    }

    /**
     * Get all unique government agencies
     */
    public static function getGovernmentAgencies()
    {
        return self::select('government_agency')
                  ->distinct()
                  ->whereNotNull('government_agency')
                  ->orderBy('government_agency')
                  ->pluck('government_agency');
    }

    /**
     * Get all unique office departments
     */
    public static function getOfficeDepartments()
    {
        return self::select('office_department_division')
                  ->distinct()
                  ->whereNotNull('office_department_division')
                  ->orderBy('office_department_division')
                  ->pluck('office_department_division');
    }

    /**
     * Get all unique positions with their count
     */
    public static function getPositionsWithCount()
    {
        return self::select('position_designation')
                  ->selectRaw('COUNT(*) as count')
                  ->distinct()
                  ->whereNotNull('position_designation')
                  ->groupBy('position_designation')
                  ->orderBy('position_designation')
                  ->get();
    }

    /**
     * Get all unique agencies with their count
     */
    public static function getAgenciesWithCount()
    {
        return self::select('government_agency')
                  ->selectRaw('COUNT(*) as count')
                  ->distinct()
                  ->whereNotNull('government_agency')
                  ->groupBy('government_agency')
                  ->orderBy('government_agency')
                  ->get();
    }

    /**
     * Get all unique departments with their count
     */
    public static function getDepartmentsWithCount()
    {
        return self::select('office_department_division')
                  ->selectRaw('COUNT(*) as count')
                  ->distinct()
                  ->whereNotNull('office_department_division')
                  ->groupBy('office_department_division')
                  ->orderBy('office_department_division')
                  ->get();
    }
}