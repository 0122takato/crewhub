<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'name',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_members')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    public function isProjectRoom(): bool
    {
        return $this->type === 'project';
    }

    public function isDirectRoom(): bool
    {
        return $this->type === 'direct';
    }

    public function isGroupRoom(): bool
    {
        return $this->type === 'group';
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
