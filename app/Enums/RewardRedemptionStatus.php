<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum RewardRedemptionStatus: string implements HasColor, HasIcon, HasLabel
{
    case NEW = 'new';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): string|Htmlable|null
    {
        return __($this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NEW => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::NEW => PhosphorIcons::SparkleDuotone,
            self::APPROVED => PhosphorIcons::CheckCircleDuotone,
            self::REJECTED => PhosphorIcons::XCircleDuotone,
        };
    }
}
