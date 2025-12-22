<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'date',
        'start_time',
        'end_time',
        'break_minutes',
        'capacity',
        'confirmed_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'break_minutes' => 'integer',
            'capacity' => 'integer',
            'confirmed_count' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ShiftApplication::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function approvedApplications(): HasMany
    {
        return $this->hasMany(ShiftApplication::class)->where('status', 'approved');
    }

    public function hasCapacity(): bool
    {
        return $this->confirmed_count < $this->capacity;
    }

    public function getRemainingCapacityAttribute(): int
    {
        return $this->capacity - $this->confirmed_count;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }
}
