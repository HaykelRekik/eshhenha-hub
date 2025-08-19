<?php

declare(strict_types=1);

namespace App\Services\Wallets;

use App\Enums\WalletTransactionType;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class WalletTransactionService
{
    /**
     * @throws Throwable
     */
    public function createTransaction(
        Wallet $wallet,
        WalletTransactionType $type,
        float $amount,
        ?string $reason = null,
        ?array $metadata = []
    ): WalletTransaction {
        $absoluteAmount = abs($amount);

        if ($this->isDebit($type) && $wallet->balance < $absoluteAmount) {
            throw new Exception(__('Insufficient wallet balance for this operation.'));
        }

        return DB::transaction(function () use ($wallet, $type, $absoluteAmount, $reason, $metadata) {
            $balanceBefore = $wallet->balance;

            $newBalance = $this->isDebit($type)
                ? $balanceBefore - $absoluteAmount
                : $balanceBefore + $absoluteAmount;

            if (null !== $reason && '' !== $reason && '0' !== $reason) {
                $metadata['reason'] = $reason;
            }

            $transaction = $wallet->transactions()->create([
                'amount' => $absoluteAmount,
                'type' => $type,
                'balance_after' => $newBalance,
                'metadata' => $metadata,
                'user_id' => $wallet->user_id,
            ]);

            $wallet->update([
                'balance' => $newBalance,
                'last_operation_at' => now(),
            ]);

            return $transaction;
        });
    }

    /**
     * Determines if a transaction type represents a debit (a reduction in balance).
     */
    protected function isDebit(WalletTransactionType $type): bool
    {
        return in_array($type, [
            WalletTransactionType::WITHDRAWAL,
            WalletTransactionType::TRANSFER_OUT,
            WalletTransactionType::EXPIRATION,
            WalletTransactionType::FEE,
        ], true);
    }
}
