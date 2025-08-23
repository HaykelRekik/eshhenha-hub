<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Operation;

class PricingRuleTypeSelect
{
    public static function make(): Select
    {
        return Select::make('type')
            ->label(__('Pricing rule for'))
            ->options(PricingRuleType::class)
            ->required()
            ->live()
            ->hiddenOn(Operation::Edit)
            ->afterStateUpdated(function (Set $set): void {
                $set(key: 'shipping_company_id', state: null);
                $set(key: 'company_id', state: null);
                $set(key: 'user_id', state: null);
            });
    }
}
