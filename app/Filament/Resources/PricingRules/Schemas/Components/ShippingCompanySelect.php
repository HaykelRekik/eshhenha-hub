<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;

class ShippingCompanySelect
{
    public static function make(): Select
    {
        return Select::make('shipping_company_id')
            ->label(__('Shipping Company'))
            ->relationship('shippingCompany', 'name')
            ->requiredIf('type', [
                PricingRuleType::COMPANY_SHIPPING_COMPANY->value,
                PricingRuleType::SHIPPING_COMPANY->value,
                PricingRuleType::CUSTOMER_SHIPPING_COMPANY->value,
            ])
            ->visible(fn(Get $get): bool => in_array($get('type'), [PricingRuleType::COMPANY_SHIPPING_COMPANY, PricingRuleType::CUSTOMER_SHIPPING_COMPANY, PricingRuleType::SHIPPING_COMPANY]))
            ->afterStateUpdatedJs(
                <<<'JS'
                $set('company_id', null)
                JS
            );
    }
}
