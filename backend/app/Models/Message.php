<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'content',
        'type',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }
}
