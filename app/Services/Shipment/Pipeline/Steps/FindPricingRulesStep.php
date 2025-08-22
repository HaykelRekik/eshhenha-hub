<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\Services\Shipment\Pipeline\ShipmentPriceCalculationContext;
use App\Services\Shipment\PricingRuleFinderService;

/**
 * Pipeline step to find applicable pricing rules for all shipping companies
 */
final readonly class FindPricingRulesStep
{
    public function __construct(
        private PricingRuleFinderService $pricingRuleFinder
    ) {}

    /**
     * Execute the step to find applicable pricing rules
     *
     * @param  ShipmentPriceCalculationContext  $context  The current context
     * @param  callable  $next  The next step in the pipeline
     * @return ShipmentPriceCalculationContext The updated context
     */
    public function handle(ShipmentPriceCalculationContext $context, callable $next): ShipmentPriceCalculationContext
    {
        $pricingRules = $this->pricingRuleFinder->findApplicableRulesForCompanies(
            shippingCompanyIds: $context->getShippingCompanyIds(),
            userId: $context->getUserId(),
            companyId: $context->getCompanyId(),
            weight: $context->getWeight()
        );

        $updatedContext = $context->withPricingRules($pricingRules);

        return $next($updatedContext);
    }
}
