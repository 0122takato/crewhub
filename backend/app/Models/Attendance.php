<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'clock_in',
        'clock_out',
        'break_minutes',
        'status',
        'work_report',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'break_minutes' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AttendancePhoto::class);
    }

    public function paymentDetails(): HasMany
    {
        return $this->hasMany(PaymentDetail::class);
    }

    public function getWorkHoursAttribute(): ?float
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }

        $minutes = $this->clock_in->diffInMinutes($this->clock_out) - ($this->break_minutes ?? 0);
        return round($minutes / 60, 2);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
