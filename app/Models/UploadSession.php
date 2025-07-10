<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UploadSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_name',
        'status',
        'notes',
        'total_records',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'total_records' => 'integer',
    ];

    /**
     * Get the user that owns the upload session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all data records for this session.
     */
    public function dataRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class);
    }

    /**
     * Get only draft records for this session.
     */
    public function draftRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class)->where('status', 'draft');
    }

    /**
     * Get only submitted records for this session.
     */
    public function submittedRecords(): HasMany
    {
        return $this->hasMany(PydiDataRecord::class)->where('status', 'submitted');
    }

    /**
     * Check if session is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if session is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Mark session as submitted.
     */
    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'total_records' => $this->dataRecords()->count(),
        ]);
    }

    /**
     * Mark session as cancelled.
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Update total records count.
     */
    public function updateRecordsCount(): void
    {
        $this->update([
            'total_records' => $this->dataRecords()->count(),
        ]);
    }

    /**
     * Scope for active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for submitted sessions.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope for user sessions.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
