<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'created_by',
        'title',
        'description',
        'venue_name',
        'venue_address',
        'start_date',
        'end_date',
        'hourly_wage',
        'transportation_fee',
        'requirements',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'hourly_wage' => 'integer',
            'transportation_fee' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function chatRoom(): HasOne
    {
        return $this->hasOne(ChatRoom::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
