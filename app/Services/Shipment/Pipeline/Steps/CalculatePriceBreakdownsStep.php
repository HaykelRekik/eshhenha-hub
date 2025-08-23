<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\DTOs\Shipment\ShippingCompanyPriceBreakdown;

/**
 * Pipeline step to calculate price breakdowns for each shipping company
 */
final readonly class CalculatePriceBreakdownsStep
{
    public $priceBreakdownCalculator;

    /**
     * Execute the step to calculate price breakdowns
     *
     * @param  array  $data  ['request' => ShipmentPriceCalculationRequest, 'potential_shipping_companies' => array]
     * @return array<int, ShippingCompanyPriceBreakdown> The updated context
     */
    public function handle(array $data, callable $next): array
    {
        $request = $data['request'];
        $results = [];

        foreach ($data['potential_shipping_companies'] as $entry) {
            $company = $entry['company'];
            $rule = $entry['rule'];

            $priceBreakdown = $this->priceBreakdownCalculator->calculate(
                pricingRule: $rule,
                shippingCompany: $company,
                weight: $request->weight,
                homePickup: $request->homePickup,
                shipmentValue: $request->shipmentValue,
                insured: $request->insured
            );

            $results[$company->id] = new ShippingCompanyPriceBreakdown(
                name: $company->name,
                breakdown: $priceBreakdown
            );
        }

        return $next($results);
    }
}
