<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\DTOs\Shipment\ShippingCompanyPriceBreakdown;
use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Services\Shipment\Pipeline\Steps\CalculatePriceBreakdownsStep;
use App\Services\Shipment\Pipeline\Steps\FindAvailableShippingCompaniesStep;
use App\Services\Shipment\Pipeline\Steps\FindPricingRulesStep;
use App\Services\Shipment\Pipeline\Steps\MatchApplicableRulesStep;
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
     * Calculate shipment prices for all available shipping companies
     *
     * @param  ShipmentPriceCalculationRequest  $request  The calculation request
     * @return array<int, ShippingCompanyPriceBreakdown> Array of price breakdowns indexed by shipping company ID
     */
    public function calculatePrices(ShipmentPriceCalculationRequest $request): array
    {
        $pipeline = app(Pipeline::class);

        $data = ['request' => $request];

        return $pipeline->send($data)
            ->through([
                $this->findCompaniesStep,
                $this->findPricingRulesStep,
                new MatchApplicableRulesStep(),
                $this->calculateBreakdownsStep,
            ])
            ->thenReturn();
    }
}
