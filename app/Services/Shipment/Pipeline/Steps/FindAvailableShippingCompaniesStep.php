<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\Models\ShippingCompany;
use App\Services\Shipment\Pipeline\ShipmentPriceCalculationContext;
use Illuminate\Support\Collection;

/**
 * Pipeline step to find available shipping companies for the given region
 */
final readonly class FindAvailableShippingCompaniesStep
{
    /**
     * Execute the step to find available shipping companies
     *
     * @param  ShipmentPriceCalculationContext  $context  The current context
     * @param  callable  $next  The next step in the pipeline
     * @return ShipmentPriceCalculationContext The updated context
     */
    public function handle(ShipmentPriceCalculationContext $context, callable $next): ShipmentPriceCalculationContext
    {
        $availableShippingCompanies = $this->getAvailableShippingCompanies($context->getRecipientRegionId());

        $updatedContext = $context->withAvailableShippingCompanies($availableShippingCompanies);

        // If no companies are available, return early with empty results
        if ( ! $updatedContext->hasAvailableShippingCompanies()) {
            return $updatedContext->withResults([]);
        }

        return $next($updatedContext);
    }

    /**
     * Get shipping companies that can deliver to the specified region
     *
     * @param  int  $regionId  The recipient region ID
     * @return Collection<int, ShippingCompany> Collection of available shipping companies
     */
    private function getAvailableShippingCompanies(int $regionId): Collection
    {
        return ShippingCompany::query()
            ->OperatesInRegion($regionId)
            ->where('is_active', true)
            ->get()
            ->collect();
    }
}
