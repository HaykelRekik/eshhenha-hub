<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasIcon, HasLabel
{
    case ADMIN = 'admin';
    case COMPANY = 'company';
    case USER = 'customer';

    public function getLabel(): ?string
    {
        return ucfirst(__($this->value));
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ADMIN => Color::Rose,
            self::COMPANY => Color::Sky,
            self::USER => Color::Teal,
        };
    }

    public function getIcon(): string|null|\BackedEnum
    {
        return match ($this) {
            self::ADMIN => PhosphorIcons::ShieldStarDuotone,
            self::COMPANY => PhosphorIcons::BuildingOfficeDuotone,
            self::USER => PhosphorIcons::UserDuotone,
        };
    }
}
