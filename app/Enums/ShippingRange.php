<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShippingRange: string implements HasColor, HasLabel
{
    case LOCAL = 'local';
    case INTERNATIONAL = 'international';
    case ALL = 'all';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOCAL => __('local'),
            self::INTERNATIONAL => __('international'),
            self::ALL => __('local & international'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LOCAL => Color::Sky,
            self::INTERNATIONAL => Color::Orange,
            self::ALL => Color::Rose,
        };
    }
}
