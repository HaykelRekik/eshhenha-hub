<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WalletTransactionType: string implements HasColor, HasIcon, HasLabel
{
    case DEPOSIT = 'deposit';           // User adds funds (e.g., via Stripe/MyFatoorah)
    case WITHDRAWAL = 'withdrawal';     // User spends wallet balance
    case LOYALTY_CONVERSION = 'loyalty points conversion'; // Points → Balance conversion
    case ADJUSTMENT = 'adjustment';     // Manual admin adjustment (+/-)
    case REFUND = 'refund';             // Reversal of a prior transaction
    case TRANSFER_IN = 'transfer in';   // Received from another user
    case TRANSFER_OUT = 'transfer out'; // Sent to another user
    case EXPIRATION = 'expiration';     // Balance expired (if TTL applies)
    case FEE = 'fee';                   // Deduction (e.g., processing fee)

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DEPOSIT, self::LOYALTY_CONVERSION, self::TRANSFER_IN, self::REFUND => 'success',
            self::WITHDRAWAL, self::TRANSFER_OUT, self::FEE => 'danger',
            self::ADJUSTMENT => 'warning',
            self::EXPIRATION => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DEPOSIT => 'heroicon-o-arrow-down-tray',
            self::WITHDRAWAL => 'heroicon-o-arrow-up-tray',
            self::LOYALTY_CONVERSION => 'heroicon-o-arrows-right-left',
            self::ADJUSTMENT => 'heroicon-o-adjustments-vertical',
            self::REFUND => 'heroicon-o-arrow-uturn-left',
            self::TRANSFER_IN => 'heroicon-o-inbox-arrow-down',
            self::TRANSFER_OUT => 'heroicon-o-paper-airplane',
            self::EXPIRATION => 'heroicon-o-clock',
            self::FEE => 'heroicon-o-banknotes',
        };
    }

    public function getLabel(): ?string
    {
        return __($this->value);
    }
}
