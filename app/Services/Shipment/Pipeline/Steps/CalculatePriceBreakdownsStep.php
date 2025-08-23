<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\DTOs\Shipment\PriceBreakdown;
use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\DTOs\Shipment\ShippingCompanyPriceBreakdown;
use App\Enums\ShippingCompanyInsuranceType;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use Illuminate\Support\Collection;

final class CalculatePriceBreakdownsStep
{
    public function __invoke(array $data): array
    {
        /** @var ShipmentPriceCalculationRequest $request */
        $request = $data['request'];
        /** @var Collection<int, ShippingCompany> $shippingCompanies */
        $shippingCompanies = $data['shipping_companies'];
        /** @var array<int, PricingRule> $matchedRules */
        $matchedRules = $data['matched_rules'];

        $priceBreakdowns = [];

        foreach ($matchedRules as $shippingCompanyId => $rule) {
            /** @var ShippingCompany|null $shippingCompany */
            $shippingCompany = $shippingCompanies->get($shippingCompanyId);

            if ( ! $shippingCompany) {
                continue;
            }

            $basePrice = $rule->local_price_per_kg * $request->weight;
            $insuranceCost = $this->calculateInsurance($shippingCompany, $request);
            $homePickupCost = $request->homePickup ? $shippingCompany->home_pickup_cost : 0;

            $subtotal = $basePrice + $insuranceCost + $homePickupCost;
            $tax = $subtotal * ($shippingCompany->local_tax_rate / 100);
            $total = $subtotal + $tax;

            $breakdown = new PriceBreakdown(
                basePrice: $basePrice,
                insuranceCost: $insuranceCost,
                homePickupCost: $homePickupCost,
                tax: $tax,
                total: $total,
            );

            $priceBreakdowns[$shippingCompanyId] = new ShippingCompanyPriceBreakdown(
                name: $shippingCompany->name,
                breakdown: $breakdown,
            );
        }

        return $priceBreakdowns;
    }

    private function calculateInsurance(ShippingCompany $shippingCompany, ShipmentPriceCalculationRequest $request): float
    {
        if ( ! $request->insured) {
            return 0.0;
        }

        return match ($shippingCompany->insurance_type) {
            ShippingCompanyInsuranceType::PERCENTAGE => $request->shipmentValue * ($shippingCompany->insurance_value / 100),
            ShippingCompanyInsuranceType::AMOUNT => $shippingCompany->insurance_value,
            default => 0.0,
        };
    }
}
