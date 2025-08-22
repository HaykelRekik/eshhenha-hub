<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\DTOs\Shipment\PriceBreakdown;
use App\Enums\ShippingCompanyInsuranceType;
use App\Models\PricingRule;
use App\Models\ShippingCompany;

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

    private function calculateBasePrice(PricingRule $pricingRule, float $weight, bool $isInternational = false): float
    {
        return ($isInternational ? $pricingRule->international_price_per_kg : $pricingRule->local_price_per_kg) * $weight;
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

    private function calculateTaxAmount(float $basePrice, float $homePickupCost, float $insuranceCost, bool $isInternational = false): float
    {
        $taxableAmount = $basePrice + $homePickupCost + $insuranceCost;

        return $taxableAmount * ($isInternational ? self::INTERNATIONAL_TAX_RATE : self::LOCAL_TAX_RATE);
    }
}
