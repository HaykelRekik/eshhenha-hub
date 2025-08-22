<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\Models\PricingRule;
use App\Models\User;
use Illuminate\Support\Collection;

readonly class PricingRuleFinderService
{
    /**
     * Find applicable pricing rules for multiple shipping companies in a single query
     */
    public function findApplicableRulesForCompanies(
        Collection $shippingCompanyIds,
        ?int $userId,
        ?int $companyId,
        float $weight
    ): Collection {
        $rules = collect();

        // 1. Try to find specific rules: shipping company + user/company + weight
        $specificRules = $this->findSpecificRules($shippingCompanyIds, $userId, $companyId, $weight);
        $rules = $rules->merge($specificRules);

        // Get remaining shipping companies that don't have specific rules
        $remainingCompanyIds = $shippingCompanyIds->diff($rules->pluck('shipping_company_id'));

        if ($remainingCompanyIds->isNotEmpty()) {
            // 2. Try to find user/company specific rules (without shipping company)
            $userCompanyRules = $this->findUserCompanyRules($remainingCompanyIds, $userId, $companyId, $weight);
            $rules = $rules->merge($userCompanyRules);

            // Get remaining shipping companies that don't have user/company specific rules
            $remainingCompanyIds = $remainingCompanyIds->diff($userCompanyRules->pluck('shipping_company_id'));

            if ($remainingCompanyIds->isNotEmpty()) {
                // 3. Try to find shipping company specific rules (without user/company)
                $shippingCompanyRules = $this->findShippingCompanyRules($remainingCompanyIds, $weight);
                $rules = $rules->merge($shippingCompanyRules);

                // Get remaining shipping companies that don't have shipping company specific rules
                $remainingCompanyIds = $remainingCompanyIds->diff($shippingCompanyRules->pluck('shipping_company_id'));

                if ($remainingCompanyIds->isNotEmpty()) {
                    // 4. Fallback to global rules
                    $globalRules = $this->findGlobalRules($remainingCompanyIds, $weight);
                    $rules = $rules->merge($globalRules);
                }
            }
        }

        return $rules;
    }

    /**
     * Legacy method for backward compatibility - finds single rule
     */
    public function findApplicableRule(
        int $shippingCompanyId,
        ?int $userId,
        ?int $companyId,
        float $weight
    ): ?PricingRule {
        $rules = $this->findApplicableRulesForCompanies(
            collect([$shippingCompanyId]),
            $userId,
            $companyId,
            $weight
        );

        return $rules->first();
    }

    /**
     * Find specific rules: shipping company + user/company + weight
     */
    private function findSpecificRules(Collection $shippingCompanyIds, ?int $userId, ?int $companyId, float $weight): Collection
    {
        $query = PricingRule::query()
            ->whereIn('shipping_company_id', $shippingCompanyIds)
            ->forWeight($weight);

        if ($userId) {
            $query->forCustomers($userId);
        } elseif ($companyId) {
            $query->forCompany($companyId);
        }

        return $query->get()->collect();
    }

    /**
     * Find user/company specific rules (without shipping company)
     */
    private function findUserCompanyRules(Collection $shippingCompanyIds, ?int $userId, ?int $companyId, float $weight): Collection
    {
        $query = PricingRule::query()
            ->whereIn('shipping_company_id', $shippingCompanyIds)
            ->forWeight($weight)
            ->whereNull('user_id')
            ->whereNull('company_id');

        if ($userId) {
            $query->forCustomers($userId);
        } elseif ($companyId) {
            $query->forCompany($companyId);
        }

        return $query->get()->collect();
    }

    /**
     * Find shipping company specific rules (without user/company)
     */
    private function findShippingCompanyRules(Collection $shippingCompanyIds, float $weight): Collection
    {
        return PricingRule::query()
            ->whereIn('shipping_company_id', $shippingCompanyIds)
            ->forWeight($weight)
            ->whereNull('user_id')
            ->whereNull('company_id')
            ->get()
            ->collect();
    }

    /**
     * Find global rules where all IDs are null
     */
    private function findGlobalRules(Collection $shippingCompanyIds, float $weight): Collection
    {
        return PricingRule::query()
            ->whereIn('shipping_company_id', $shippingCompanyIds)
            ->forWeight($weight)
            ->whereNull('user_id')
            ->whereNull('company_id')
            ->whereNull('shipping_company_id')
            ->get()
            ->collect();
    }
}
