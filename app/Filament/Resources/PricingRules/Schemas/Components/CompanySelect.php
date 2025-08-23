<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;

class CompanySelect
{
    public static function make(): Select
    {
        return Select::make('company_id')
            ->label(__('Company'))
            ->options(function (Get $get) {
                if (PricingRuleType::COMPANY === $get('type')) {
                    return Company::whereIsActive(true)->pluck('name', 'id');
                }

                return Company::whereIsActive(true)
                    ->whereRelation('contracts', 'shipping_company_id', $get('shipping_company_id'))
                    ->pluck('name', 'id');
            })
            ->requiredIf('type', [
                PricingRuleType::COMPANY_SHIPPING_COMPANY->value,
                PricingRuleType::COMPANY->value,
            ])
            ->visible(fn (Get $get): bool => in_array($get('type'), [PricingRuleType::COMPANY_SHIPPING_COMPANY, PricingRuleType::COMPANY]))
            ->searchable()
            ->preload();
    }
}
