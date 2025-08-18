<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\WalletTransaction;
use Illuminate\Support\Str;

class WalletTransactionObserver
{
    public function creating(WalletTransaction $walletTransaction): void
    {
        $walletTransaction->identifier = Str::ulid();
    }

    public function saving(WalletTransaction $walletTransaction): void
    {
        $walletTransaction->performed_by = auth()->id();
    }
}
