<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Enums\PricingRuleType;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use Closure;
use Illuminate\Support\Collection;

final class MatchApplicableRulesStep
{
    public function __invoke(array $data, Closure $next)
    {
        /** @var ShipmentPriceCalculationRequest $request */
        $request = $data['request'];
        /** @var Collection<int, ShippingCompany> $shippingCompanies */
        $shippingCompanies = $data['shipping_companies'];
        /** @var Collection<int, PricingRule> $pricingRules */
        $pricingRules = $data['pricing_rules'];

        $matchedRules = [];
        $processedShippingCompanyIds = [];

        $rulesByType = $pricingRules->groupBy('type');

        // Priority 1: Customer/Company & Shipping Company Specific
        $this->matchComplexRules($rulesByType, $request, $processedShippingCompanyIds, $matchedRules);

        // Priority 2: Customer/Company Specific
        $this->matchEntitySpecificRules($rulesByType, $request, $shippingCompanies, $processedShippingCompanyIds, $matchedRules);

        // Priority 3: Shipping Company Specific
        $this->matchShippingCompanyRules($rulesByType, $processedShippingCompanyIds, $matchedRules);

        // Priority 4: Global Rule
        $this->matchGlobalRule($rulesByType, $shippingCompanies, $processedShippingCompanyIds, $matchedRules);

        $data['matched_rules'] = $matchedRules;

        return $next($data);
    }

    private function matchComplexRules(Collection $rulesByType, ShipmentPriceCalculationRequest $request, array &$processedShippingCompanyIds, array &$matchedRules): void
    {
        $customerShippingCompanyRules = $rulesByType->get(PricingRuleType::CUSTOMER_SHIPPING_COMPANY->value, new Collection());
        foreach ($customerShippingCompanyRules as $rule) {
            if ($rule->user_id === $request->userId && ! in_array($rule->shipping_company_id, $processedShippingCompanyIds)) {
                $matchedRules[$rule->shipping_company_id] = $rule;
                $processedShippingCompanyIds[] = $rule->shipping_company_id;
            }
        }

        $companyShippingCompanyRules = $rulesByType->get(PricingRuleType::COMPANY_SHIPPING_COMPANY->value, new Collection());
        foreach ($companyShippingCompanyRules as $rule) {
            if ($rule->company_id === $request->companyId && ! in_array($rule->shipping_company_id, $processedShippingCompanyIds)) {
                $matchedRules[$rule->shipping_company_id] = $rule;
                $processedShippingCompanyIds[] = $rule->shipping_company_id;
            }
        }
    }

    private function matchEntitySpecificRules(Collection $rulesByType, ShipmentPriceCalculationRequest $request, Collection $shippingCompanies, array &$processedShippingCompanyIds, array &$matchedRules): void
    {
        $customerRule = $rulesByType->get(PricingRuleType::CUSTOMER->value, new Collection())->firstWhere('user_id', $request->userId);
        $companyRule = $rulesByType->get(PricingRuleType::COMPANY->value, new Collection())->firstWhere('company_id', $request->companyId);

        $applicableRule = $customerRule ?? $companyRule;

        if ($applicableRule) {
            foreach ($shippingCompanies as $shippingCompany) {
                if ( ! in_array($shippingCompany->id, $processedShippingCompanyIds)) {
                    $matchedRules[$shippingCompany->id] = $applicableRule;
                    $processedShippingCompanyIds[] = $shippingCompany->id;
                }
            }
        }
    }

    private function matchShippingCompanyRules(Collection $rulesByType, array &$processedShippingCompanyIds, array &$matchedRules): void
    {
        $shippingCompanyRules = $rulesByType->get(PricingRuleType::SHIPPING_COMPANY->value, new Collection());
        foreach ($shippingCompanyRules as $rule) {
            if ( ! in_array($rule->shipping_company_id, $processedShippingCompanyIds)) {
                $matchedRules[$rule->shipping_company_id] = $rule;
                $processedShippingCompanyIds[] = $rule->shipping_company_id;
            }
        }
    }

    private function matchGlobalRule(Collection $rulesByType, Collection $shippingCompanies, array &$processedShippingCompanyIds, array &$matchedRules): void
    {
        $globalRule = $rulesByType->get(PricingRuleType::GLOBAL->value, new Collection())->first();

        if ($globalRule) {
            foreach ($shippingCompanies as $shippingCompany) {
                if ( ! in_array($shippingCompany->id, $processedShippingCompanyIds)) {
                    $matchedRules[$shippingCompany->id] = $globalRule;
                }
            }
        }
    }
}
