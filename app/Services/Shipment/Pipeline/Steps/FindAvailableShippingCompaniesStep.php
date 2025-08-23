<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\Models\ShippingCompany;

/**
 * Pipeline step to find available shipping companies for the given region
 */
final readonly class FindAvailableShippingCompaniesStep
{
    /**
     * Execute the step to find available shipping companies
     *
     * @param  array  $data  ['request' => ShipmentPriceCalculationRequest]
     * @param  callable  $next  The next step in the pipeline
     * @return array The updated context
     */
    public function handle(array $data, callable $next): array
    {
        $request = $data['request'];
        $regionId = $request->recipientRegionId;
        $homePickup = $request->homePickup;

        $shippingCompanies = ShippingCompany::query()
            ->OperatesInRegion($regionId)
            ->where('is_active', true)
            ->when($homePickup, fn ($q) => $q->whereNotNull('home_pickup_cost')->where('home_pickup_cost', '>', 0))
            ->get();

        $data['shipping_companies'] = $shippingCompanies;

        return $next($data);
    }
}
