<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PricingRuleType: string implements HasLabel
{
    case GLOBAL = 'global';
    case CUSTOMER = 'customer';
    case COMPANY = 'company';
    case SHIPPING_COMPANY = 'shipping company';
    case CUSTOMER_SHIPPING_COMPANY = 'customer & shipping company';
    case COMPANY_SHIPPING_COMPANY = 'company & shipping company';

    /**
     * A static, testable method to determine the PricingRuleType based on given IDs.
     */
    public static function determineType(?int $customerId, ?int $companyId, ?int $shippingCompanyId): PricingRuleType
    {
        // We use !! to cast the IDs to booleans (true if not null/0, false if null/0)
        $state = [
            'customer' => (bool) $customerId,
            'company' => (bool) $companyId,
            'shipping' => (bool) $shippingCompanyId,
        ];

        return match (true) {
            $state['customer'] && $state['shipping'] => PricingRuleType::CUSTOMER_SHIPPING_COMPANY,
            $state['company'] && $state['shipping'] => PricingRuleType::COMPANY_SHIPPING_COMPANY,

            $state['customer'] => PricingRuleType::CUSTOMER,
            $state['company'] => PricingRuleType::COMPANY,
            $state['shipping'] => PricingRuleType::SHIPPING_COMPANY,

            default => PricingRuleType::GLOBAL,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return __($this->value);
    }
}
