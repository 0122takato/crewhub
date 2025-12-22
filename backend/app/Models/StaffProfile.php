<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'gender',
        'postal_code',
        'prefecture',
        'city',
        'address',
        'bank_name',
        'bank_branch',
        'bank_account_type',
        'bank_account_number',
        'bank_account_holder',
        'profile_photo_path',
        'id_verified_at',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'id_verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isIdVerified(): bool
    {
        return $this->id_verified_at !== null;
    }

    public function getFullAddressAttribute(): string
    {
        return implode(' ', array_filter([
            $this->prefecture,
            $this->city,
            $this->address,
        ]));
    }
}
