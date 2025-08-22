<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\Enums\ShippingCompanyInsuranceType;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use App\Services\Shipment\DTOs\PriceBreakdown;

readonly class PriceBreakdownCalculatorService
{
    private const LOCAL_TAX_RATE = 0.15;

    private const INTERNATIONAL_TAX_RATE = 0.00;

    public function calculate(
        PricingRule $pricingRule,
        ShippingCompany $shippingCompany,
        float $weight,
        bool $homePickup,
        float $shipmentValue
    ): PriceBreakdown {
        $basePrice = $this->calculateBasePrice($pricingRule, $weight);
        $homePickupCost = $this->calculateHomePickupCost($shippingCompany, $homePickup);
        $insuranceCost = $this->calculateInsuranceCost($shippingCompany, $shipmentValue);
        $taxAmount = $this->calculateTaxAmount($basePrice, $homePickupCost, $insuranceCost);

        $total = $basePrice + $homePickupCost + $insuranceCost + $taxAmount;

        return new PriceBreakdown(
            basePrice: $basePrice,
            homePickupCost: $homePickupCost,
            insuranceCost: $insuranceCost,
            taxAmount: $taxAmount,
            total: $total,
        );
    }

    private function calculateBasePrice(PricingRule $pricingRule, float $weight): float
    {
        // For now, assuming local pricing. In the future, this could be enhanced
        // to determine local vs international based on recipient location
        return $pricingRule->local_price_per_kg * $weight;
    }

    private function calculateHomePickupCost(ShippingCompany $shippingCompany, bool $homePickup): float
    {
        if ( ! $homePickup || ! $shippingCompany->hasHomePickup) {
            return 0.0;
        }

        return $shippingCompany->home_pickup_cost ?? 0.0;
    }

    private function calculateInsuranceCost(ShippingCompany $shippingCompany, float $shipmentValue): float
    {
        if ( ! $shippingCompany->insurance_type || ! $shippingCompany->insurance_value) {
            return 0.0;
        }

        return match ($shippingCompany->insurance_type) {
            ShippingCompanyInsuranceType::PERCENTAGE => ($shipmentValue * $shippingCompany->insurance_value) / 100,
            ShippingCompanyInsuranceType::AMOUNT => $shippingCompany->insurance_value,
            default => 0.0,
        };
    }

    private function calculateTaxAmount(float $basePrice, float $homePickupCost, float $insuranceCost): float
    {
        $taxableAmount = $basePrice + $homePickupCost + $insuranceCost;

        // For now, assuming local shipment (15% tax)
        // In the future, this could be enhanced to determine local vs international
        // based on recipient location or shipping company configuration
        return $taxableAmount * self::LOCAL_TAX_RATE;
    }
}
