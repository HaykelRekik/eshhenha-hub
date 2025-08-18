<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletTransactionType;
use App\Observers\WalletTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(WalletTransactionObserver::class)]
class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'balance_after',
        'identifier',
        'external_identifier',
        'metadata',
        'wallet_id',
        'user_id',
        'performed_by',
    ];

    public static function getNavigationGroup(): ?string
    {
        return __('Financial Operations');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    protected function casts(): array
    {
        return [
            'type' => WalletTransactionType::class,
            'metadata' => 'collection',
        ];
    }
}
