<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ShippingCompanyInsuranceType: string implements HasIcon, HasLabel
{
    case PERCENTAGE = 'percentage';
    case AMOUNT = 'amount';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::PERCENTAGE => PhosphorIcons::PercentDuotone,
            self::AMOUNT => PhosphorIcons::MoneyWavyDuotone,
        };
    }
}
