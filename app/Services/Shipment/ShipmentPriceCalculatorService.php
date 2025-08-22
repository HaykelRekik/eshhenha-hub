<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Services\Shipment\Pipeline\ShipmentPriceCalculationContext;
use App\Services\Shipment\Pipeline\Steps\CalculatePriceBreakdownsStep;
use App\Services\Shipment\Pipeline\Steps\FindAvailableShippingCompaniesStep;
use App\Services\Shipment\Pipeline\Steps\FindPricingRulesStep;
use Illuminate\Pipeline\Pipeline;

/**
 * Service for calculating shipment prices using Laravel's Pipeline pattern
 */
final readonly class ShipmentPriceCalculatorService
{
    /**
     * @param  FindAvailableShippingCompaniesStep  $findCompaniesStep  Step to find available shipping companies
     * @param  FindPricingRulesStep  $findPricingRulesStep  Step to find applicable pricing rules
     * @param  CalculatePriceBreakdownsStep  $calculateBreakdownsStep  Step to calculate price breakdowns
     */
    public function __construct(
        private FindAvailableShippingCompaniesStep $findCompaniesStep,
        private FindPricingRulesStep $findPricingRulesStep,
        private CalculatePriceBreakdownsStep $calculateBreakdownsStep
    ) {}

    /**
     * Create a new instance of the service with default dependencies
     */
    public static function make(): self
    {
        return new self(
            findCompaniesStep: new FindAvailableShippingCompaniesStep(),
            findPricingRulesStep: new FindPricingRulesStep(
                new PricingRuleFinderService()
            ),
            calculateBreakdownsStep: new CalculatePriceBreakdownsStep(
                new PriceBreakdownCalculatorService()
            )
        );
    }

    /**
     * Calculate shipment prices for all available shipping companies
     *
     * @param  ShipmentPriceCalculationRequest  $request  The calculation request
     * @return array<int, \App\DTOs\Shipment\ShippingCompanyPriceBreakdown> Array of price breakdowns indexed by shipping company ID
     */
    public function calculatePrices(ShipmentPriceCalculationRequest $request): array
    {
        $pipeline = app(Pipeline::class);

        $context = ShipmentPriceCalculationContext::fromRequest($request);

        $resultContext = $pipeline->send($context)
            ->through([
                $this->findCompaniesStep,
                $this->findPricingRulesStep,
                $this->calculateBreakdownsStep,
            ])
            ->thenReturn();

        return $resultContext->getResults();
    }
}
