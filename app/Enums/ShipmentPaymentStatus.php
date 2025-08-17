<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentPaymentStatus: string implements HasColor, HasLabel
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PAID => Color::Green,
            self::UNPAID => Color::Red,
        };
    }
}
