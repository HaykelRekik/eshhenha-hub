<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'last_operation_at',
        'is_locked',
        'lock_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => ! $this->attributes['is_locked'],
        );
    }

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'last_operation_at' => 'datetime',
        ];
    }

    protected function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
