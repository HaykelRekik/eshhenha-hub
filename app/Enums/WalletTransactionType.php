<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum WalletTransactionType: string implements HasColor, HasIcon, HasLabel
{
    case DEPOSIT = 'deposit';           // User adds funds (e.g., via Stripe/MyFatoorah)
    case WITHDRAWAL = 'withdrawal';     // User spends wallet balance
    case LOYALTY_CONVERSION = 'loyalty points conversion'; // Points → Balance conversion
    case BUY_LOYALTY_POINTS = 'Buy loyalty points'; // Balance → Points
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
            self::WITHDRAWAL, self::BUY_LOYALTY_POINTS, self::TRANSFER_OUT, self::FEE => 'danger',
            self::ADJUSTMENT => 'warning',
            self::EXPIRATION => 'gray',
        };
    }

    public function getIcon(): string|null|BackedEnum
    {
        return match ($this) {
            self::DEPOSIT => Heroicon::ArrowDownTray,
            self::WITHDRAWAL => Heroicon::ArrowUpTray,
            self::LOYALTY_CONVERSION => Heroicon::ArrowsRightLeft,
            self::ADJUSTMENT => Heroicon::AdjustmentsVertical,
            self::REFUND => Heroicon::ArrowUturnLeft,
            self::TRANSFER_IN => Heroicon::InboxArrowDown,
            self::TRANSFER_OUT => Heroicon::PaperAirplane,
            self::EXPIRATION => Heroicon::Clock,
            self::FEE => Heroicon::Banknotes,
            self::BUY_LOYALTY_POINTS => PhosphorIcons::Medal
        };
    }

    public function getLabel(): ?string
    {
        return __($this->value);
    }
}
