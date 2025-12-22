<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'platform',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isIos(): bool
    {
        return $this->platform === 'ios';
    }

    public function isAndroid(): bool
    {
        return $this->platform === 'android';
    }

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}
