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
     * Calculate shipment prices for all available shipping companies
     *
     * @param ShipmentPriceCalculationRequest $request The calculation request
     * @return array<int, ShippingCompanyPriceBreakdown> Array of price breakdowns indexed by shipping company ID
     */
    public function calculatePrices(ShipmentPriceCalculationRequest $request): array
    {
        $pipeline = app(Pipeline::class);

        $data = ['request' => $request];

        return $pipeline->send($data)
            ->through([
                new FindAvailableShippingCompaniesStep(),
                new FindPricingRulesStep(),
                new MatchApplicableRulesStep(),
                new CalculatePriceBreakdownsStep(),
            ])
            ->thenReturn();
    }
}
