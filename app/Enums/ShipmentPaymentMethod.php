<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentPaymentMethod: string implements HasColor, HasLabel
{
    case CREDIT_CARD = 'credit card';
    case WALLET = 'wallet';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CREDIT_CARD => Color::Green,
            self::WALLET => Color::Red,
        };
    }
}
