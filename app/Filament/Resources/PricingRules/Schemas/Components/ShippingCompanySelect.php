<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use Filament\Forms\Components\Select;

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
            ->visibleJs(
                <<<'JS'
                $get('type') === 'shipping company' ||
                $get('type') === 'customer & shipping company' ||
                $get('type') === 'company & shipping company'
                JS
            )
            ->afterStateUpdatedJs(
                <<<'JS'
                $set('company_id', null)
                JS
            );
    }
}
