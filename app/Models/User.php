<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_role',
        'active_status',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the user data associated with this user
     * KEY RELATIONSHIP: Connects to user_data table
     */
    public function userData(): HasOne
    {
        return $this->hasOne(UserData::class);
    }

    /**
     * Get all PYDP levels (levels of result) owned by this user
     * KEY RELATIONSHIP: User can have multiple levels
     */
    public function pydpLevels(): HasMany
    {
        return $this->hasMany(PydpLevel::class);
    }

    /**
     * Get all PYDP entries submitted by this user
     * Uses 'submitted_by' as the foreign key (matches your table structure)
     */
    public function submittedPydpEntries(): HasMany
    {
        return $this->hasMany(PydpDatasetEntry::class, 'submitted_by');
    }

    /**
     * Get all notifications for this user
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get full name from user_data if available
     */
    public function getFullNameAttribute(): string
    {
        if ($this->userData) {
            $name = ($this->userData->first_name ?? '') . ' ' . ($this->userData->last_name ?? '');
            return trim($name) ?: ($this->name ?? 'Unknown User');
        }
        return $this->name ?? 'Unknown User';
    }

    /**
     * Get short name (first + last only) from user_data
     */
    public function getShortNameAttribute(): string
    {
        if ($this->userData) {
            return trim(($this->userData->first_name ?? '') . ' ' . ($this->userData->last_name ?? '')) ?: ($this->name ?? 'Unknown');
        }
        return $this->name ?? 'Unknown';
    }

    /**
     * Get government agency from user_data if available
     */
    public function getAgencyAttribute(): string
    {
        return $this->userData?->government_agency ?? 'N/A';
    }

    /**
     * Get position from user_data if available
     */
    public function getPositionAttribute(): string
    {
        return $this->userData?->position_designation ?? 'N/A';
    }

    /**
     * Get office/department from user_data if available
     */
    public function getOfficeAttribute(): string
    {
        return $this->userData?->office_department_division ?? 'N/A';
    }

    /**
     * Get mobile number from user_data if available
     */
    public function getMobileAttribute(): string
    {
        return $this->userData?->mobile_number ?? 'N/A';
    }

    // ============================================
    // SCOPES - GENERAL SEARCH
    // ============================================

    /**
     * Scope to search by name or email
     */
    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        return $query->where(function ($query) use ($term) {
            $query->where('users.name', 'like', $term)
                  ->orWhere('users.email', 'like', $term);
        });
    }

    /**
     * Scope to search by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('user_role', $role);
    }

    /**
     * Scope to get active users only
     */
    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    /**
     * Scope to get inactive users only
     */
    public function scopeInactive($query)
    {
        return $query->where('active_status', false);
    }

    // ============================================
    // SCOPES - PYDP RELATED
    // ============================================

    /**
     * Scope to load with user data and PYDP entries
     */
    public function scopeWithUserData($query)
    {
        return $query->with('userData');
    }

    /**
     * Scope to search by name from user_data
     */
    public function scopeSearchByUserDataName($query, $search)
    {
        return $query->whereHas('userData', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to search by agency from user_data
     */
    public function scopeSearchByAgency($query, $agency)
    {
        return $query->whereHas('userData', function ($q) use ($agency) {
            $q->where('government_agency', 'like', "%{$agency}%");
        });
    }

    /**
     * Scope to search by position from user_data
     */
    public function scopeSearchByPosition($query, $position)
    {
        return $query->whereHas('userData', function ($q) use ($position) {
            $q->where('position_designation', 'like', "%{$position}%");
        });
    }

    /**
     * Scope to search by department from user_data
     */
    public function scopeSearchByDepartment($query, $department)
    {
        return $query->whereHas('userData', function ($q) use ($department) {
            $q->where('office_department_division', 'like', "%{$department}%");
        });
    }

    /**
     * Scope to get users with PYDP submissions
     */
    public function scopeWithPydpSubmissions($query)
    {
        return $query->whereHas('submittedPydpEntries');
    }

    /**
     * Scope to get users with pending PYDP submissions
     */
    public function scopeWithPendingSubmissions($query)
    {
        return $query->whereHas('submittedPydpEntries', function ($q) {
            $q->where('submission_status', 'submitted')
              ->where('edit_requested', false);
        });
    }

    // ============================================
    // LEGACY METHODS (Preserved for compatibility)
    // ============================================

    /**
     * Get admin account - legacy method kept for compatibility
     */
    public function adminAccount()
    {
        return $this->hasOne(User::class, 'name', 'name')->where('user_role', '!=', 'emp');
    }

    // ============================================
    // CUSTOM METHODS
    // ============================================

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_role !== 'emp';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->active_status === true;
    }

    /**
     * Get count of pending PYDP submissions
     */
    public function getPendingPydpCount(): int
    {
        return $this->submittedPydpEntries()
                    ->where('submission_status', 'submitted')
                    ->where('edit_requested', false)
                    ->count();
    }

    /**
     * Get count of approved PYDP submissions
     */
    public function getApprovedPydpCount(): int
    {
        return $this->submittedPydpEntries()
                    ->where('submission_status', 'approved')
                    ->count();
    }

    /**
     * Get count of rejected PYDP submissions
     */
    public function getRejectedPydpCount(): int
    {
        return $this->submittedPydpEntries()
                    ->where('submission_status', 'rejected')
                    ->count();
    }

    /**
     * Get total PYDP submissions count
     */
    public function getTotalPydpCount(): int
    {
        return $this->submittedPydpEntries()->count();
    }

    /**
     * Get PYDP statistics
     */
    public function getPydpStats(): array
    {
        return [
            'pending' => $this->getPendingPydpCount(),
            'approved' => $this->getApprovedPydpCount(),
            'rejected' => $this->getRejectedPydpCount(),
            'total' => $this->getTotalPydpCount(),
        ];
    }

    /**
     * Format user info for display
     */
    public function getDisplayInfo(): array
    {
        return [
            'name' => $this->full_name,
            'email' => $this->email,
            'agency' => $this->agency,
            'position' => $this->position,
            'role' => $this->user_role,
            'active' => $this->active_status,
        ];
    }
}