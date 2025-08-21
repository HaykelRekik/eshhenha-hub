<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class CustomerSelect
{
    public static function make(): Select
    {
        return Select::make('user_id')
            ->label(__('Customer'))
            ->requiredIf('type', [
                PricingRuleType::CUSTOMER_SHIPPING_COMPANY->value,
                PricingRuleType::CUSTOMER->value,
            ])
            ->relationship(
                name: 'user',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query) => $query->role(UserRole::USER)
            )
            ->visible(fn (Get $get): bool => in_array($get('type'), [PricingRuleType::CUSTOMER_SHIPPING_COMPANY, PricingRuleType::CUSTOMER]))
            ->searchable()
            ->preload();
    }
}
