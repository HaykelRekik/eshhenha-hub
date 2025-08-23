<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\Services\Shipment\PricingRuleFinderService;

final readonly class FindPricingRulesStep
{
    public function __construct(
        private PricingRuleFinderService $pricingRuleFinder
    ) {}

    /**
     * @param  array  $data  ['request' => ShipmentPriceCalculationRequest, 'shipping_companies' => Collection]
     */
    public function handle(array $data, callable $next): array
    {
        $request = $data['request'];
        $shippingCompanyIds = collect($data['shipping_companies'])->pluck('id');
        $pricingRules = $this->pricingRuleFinder->findApplicableRulesForCompanies(
            shippingCompanyIds: $shippingCompanyIds,
            userId: $request->userId,
            companyId: $request->companyId,
            weight: $request->weight
        );
        $data['pricing_rules'] = $pricingRules;

        return $next($data);
    }
}
