<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\DTOs\Shipment\ShippingCompanyPriceBreakdown;
use App\Services\Shipment\Pipeline\ShipmentPriceCalculationContext;
use App\Services\Shipment\PriceBreakdownCalculatorService;

/**
 * Pipeline step to calculate price breakdowns for each shipping company
 */
final readonly class CalculatePriceBreakdownsStep
{
    public function __construct(
        private PriceBreakdownCalculatorService $priceBreakdownCalculator
    ) {}

    /**
     * Execute the step to calculate price breakdowns
     *
     * @param  ShipmentPriceCalculationContext  $context  The current context
     * @param  callable  $next  The next step in the pipeline
     * @return ShipmentPriceCalculationContext The updated context
     */
    public function handle(ShipmentPriceCalculationContext $context, callable $next): ShipmentPriceCalculationContext
    {
        $pricingRulesMap = $context->pricingRules->keyBy('shipping_company_id');
        $results = [];

        foreach ($context->availableShippingCompanies as $shippingCompany) {
            $pricingRule = $pricingRulesMap->get($shippingCompany->id);

            if ( ! $pricingRule) {
                continue;
            }

            $priceBreakdown = $this->priceBreakdownCalculator->calculate(
                pricingRule: $pricingRule,
                shippingCompany: $shippingCompany,
                weight: $context->getWeight(),
                homePickup: $context->getHomePickup(),
                shipmentValue: $context->getShipmentValue()
            );

            $results[$shippingCompany->id] = new ShippingCompanyPriceBreakdown(
                name: $shippingCompany->name,
                breakdown: $priceBreakdown
            );
        }

        $updatedContext = $context->withResults($results);

        return $next($updatedContext);
    }
}
