<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\Models\PricingRule;
use App\Models\User;

readonly class PricingRuleFinderService
{
    public function findApplicableRule(
        int $shippingCompanyId,
        ?int $userId,
        ?int $companyId,
        float $weight
    ): ?PricingRule {
        // 1. Try to find specific rule: shipping company + user/company + weight
        $rule = $this->findSpecificRule($shippingCompanyId, $userId, $companyId, $weight);
        if ($rule) {
            return $rule;
        }

        // 2. Try to find user/company specific rule (without shipping company)
        $rule = $this->findUserCompanyRule($userId, $companyId, $weight);
        if ($rule) {
            return $rule;
        }

        // 3. Try to find shipping company specific rule (without user/company)
        $rule = $this->findShippingCompanyRule($shippingCompanyId, $weight);
        if ($rule) {
            return $rule;
        }

        // 4. Fallback to global rules
        return $this->findGlobalRule($weight);
    }

    private function findSpecificRule(int $shippingCompanyId, ?int $userId, ?int $companyId, float $weight): ?PricingRule
    {
        $query = PricingRule::query()
            ->forShippingCompany($shippingCompanyId)
            ->forWeight($weight);

        if ($userId) {
            $query->forCustomers($userId);
        } elseif ($companyId) {
            $query->forCompany($companyId);
        }

        return $query->first();
    }

    private function findUserCompanyRule(?int $userId, ?int $companyId, float $weight): ?PricingRule
    {
        $query = PricingRule::query()
            ->forWeight($weight)
            ->whereNull('shipping_company_id');

        if ($userId) {
            $query->forCustomers($userId);
        } elseif ($companyId) {
            $query->forCompany($companyId);
        }

        return $query->first();
    }

    private function findShippingCompanyRule(int $shippingCompanyId, float $weight): ?PricingRule
    {
        return PricingRule::query()
            ->forShippingCompany($shippingCompanyId)
            ->forWeight($weight)
            ->whereNull('user_id')
            ->whereNull('company_id')
            ->first();
    }

    private function findGlobalRule(float $weight): ?PricingRule
    {
        return PricingRule::query()
            ->forWeight($weight)
            ->whereNull('user_id')
            ->whereNull('company_id')
            ->whereNull('shipping_company_id')
            ->first();
    }
}
