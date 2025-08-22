<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\Models\ShippingCompany;
use App\Services\Shipment\DTOs\ShipmentPriceCalculationRequest;
use App\Services\Shipment\DTOs\ShippingCompanyPriceBreakdown;
use Illuminate\Support\Collection;

readonly class ShipmentPriceCalculatorService
{
    public function __construct(
        private readonly PricingRuleFinderService $pricingRuleFinder,
        private readonly PriceBreakdownCalculatorService $priceBreakdownCalculator,
    ) {}

    public static function make(): self
    {
        return new self(
            pricingRuleFinder: new PricingRuleFinderService(),
            priceBreakdownCalculator: new PriceBreakdownCalculatorService(),
        );
    }

    public function calculatePrices(ShipmentPriceCalculationRequest $request): array
    {
        // Get shipping companies that can deliver to the recipient region
        $availableShippingCompanies = $this->getAvailableShippingCompanies($request->recipientRegionId);

        if ($availableShippingCompanies->isEmpty()) {
            return [];
        }

        // Get all applicable pricing rules for all shipping companies in a single query
        $pricingRules = $this->pricingRuleFinder->findApplicableRulesForCompanies(
            shippingCompanyIds: $availableShippingCompanies->pluck('id'),
            userId: $request->userId,
            companyId: $request->companyId,
            weight: $request->weight
        );

        // Create a lookup map for quick access to pricing rules by shipping company ID
        $pricingRulesMap = $pricingRules->keyBy('shipping_company_id');

        $results = [];

        foreach ($availableShippingCompanies as $shippingCompany) {
            $pricingRule = $pricingRulesMap->get($shippingCompany->id);

            if ( ! $pricingRule) {
                continue;
            }

            $priceBreakdown = $this->priceBreakdownCalculator->calculate(
                pricingRule: $pricingRule,
                shippingCompany: $shippingCompany,
                weight: $request->weight,
                homePickup: $request->homePickup,
                shipmentValue: $request->shipmentValue
            );

            $results[$shippingCompany->id] = new ShippingCompanyPriceBreakdown(
                name: $shippingCompany->name,
                breakdown: $priceBreakdown
            );
        }

        return $results;
    }

    private function getAvailableShippingCompanies(int $regionId): Collection
    {
        return ShippingCompany::query()
            ->OperatesInRegion($regionId)
            ->where('is_active', true)
            ->get()
            ->collect();
    }
}
