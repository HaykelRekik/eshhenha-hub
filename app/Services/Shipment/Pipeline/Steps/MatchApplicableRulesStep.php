<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\Enums\PricingRuleType;
use Illuminate\Support\Collection;

class MatchApplicableRulesStep
{
    public function handle(array $data, callable $next): array
    {
        $request = $data['request'];
        $userId = $request->userId ?? null;
        $companyId = $request->companyId ?? null;
        $shippingCompanies = collect($data['shipping_companies']);
        $pricingRules = collect($data['pricing_rules']);

        $potentialShippingCompanies = collect();
        $addedCompanyIds = collect();

        $potentialShippingCompanies = $this->matchUserOrCompanyWithShippingCompany($shippingCompanies, $pricingRules, $userId, $companyId, $addedCompanyIds);
        $potentialShippingCompanies = $this->matchUserOrCompany($shippingCompanies, $pricingRules, $userId, $companyId, $addedCompanyIds, $potentialShippingCompanies);
        $potentialShippingCompanies = $this->matchShippingCompanyOnly($shippingCompanies, $pricingRules, $addedCompanyIds, $potentialShippingCompanies);
        $potentialShippingCompanies = $this->matchGlobalRule($shippingCompanies, $pricingRules, $addedCompanyIds, $potentialShippingCompanies);

        $data['potential_shipping_companies'] = $potentialShippingCompanies->values()->all();

        return $next($data);
    }

    private function matchUserOrCompanyWithShippingCompany(
        Collection $shippingCompanies,
        Collection $pricingRules,
        ?int $userId,
        ?int $companyId,
        Collection $addedCompanyIds
    ): Collection {
        $result = collect();
        $shippingCompanies->each(function ($shippingCompany) use ($pricingRules, $userId, $companyId, $addedCompanyIds, &$result): void {
            $shippingCompanyId = $shippingCompany->id;
            $rule = $pricingRules->first(fn ($rule): bool => PricingRuleType::determineType($userId, $companyId, $shippingCompanyId) === $rule->type
                    && ($rule->user_id === $userId || $rule->company_id === $companyId)
                    && $rule->shipping_company_id === $shippingCompanyId);
            if ($rule && ! $addedCompanyIds->contains($shippingCompanyId)) {
                $result->push(['company' => $shippingCompany, 'rule' => $rule]);
                $addedCompanyIds->push($shippingCompanyId);
            }
        });

        return $result;
    }

    private function matchUserOrCompany(
        Collection $shippingCompanies,
        Collection $pricingRules,
        ?int $userId,
        ?int $companyId,
        Collection $addedCompanyIds,
        Collection $result
    ): Collection {
        $shippingCompanies->each(function ($shippingCompany) use ($pricingRules, $userId, $companyId, $addedCompanyIds, &$result): void {
            $shippingCompanyId = $shippingCompany->id;
            $rule = $pricingRules->first(fn ($rule): bool => PricingRuleType::determineType($userId, $companyId, null) === $rule->type
                    && ($rule->user_id === $userId || $rule->company_id === $companyId));
            if ($rule && ! $addedCompanyIds->contains($shippingCompanyId)) {
                $result->push(['company' => $shippingCompany, 'rule' => $rule]);
                $addedCompanyIds->push($shippingCompanyId);
            }
        });

        return $result;
    }

    private function matchShippingCompanyOnly(
        Collection $shippingCompanies,
        Collection $pricingRules,
        Collection $addedCompanyIds,
        Collection $result
    ): Collection {
        $shippingCompanies->each(function ($shippingCompany) use ($pricingRules, $addedCompanyIds, &$result): void {
            $shippingCompanyId = $shippingCompany->id;
            $rule = $pricingRules->first(fn ($rule): bool => PricingRuleType::determineType(null, null, $shippingCompanyId) === $rule->type
                    && $rule->shipping_company_id === $shippingCompanyId);
            if ($rule && ! $addedCompanyIds->contains($shippingCompanyId)) {
                $result->push(['company' => $shippingCompany, 'rule' => $rule]);
                $addedCompanyIds->push($shippingCompanyId);
            }
        });

        return $result;
    }

    private function matchGlobalRule(
        Collection $shippingCompanies,
        Collection $pricingRules,
        Collection $addedCompanyIds,
        Collection $result
    ): Collection {
        $globalRule = $pricingRules->first(fn ($rule): bool => PricingRuleType::GLOBAL === $rule->type);
        $shippingCompanies->each(function ($shippingCompany) use ($globalRule, $addedCompanyIds, &$result): void {
            if ($globalRule && ! $addedCompanyIds->contains($shippingCompany->id)) {
                $result->push(['company' => $shippingCompany, 'rule' => $globalRule]);
                $addedCompanyIds->push($shippingCompany->id);
            }
        });

        return $result;
    }
}
