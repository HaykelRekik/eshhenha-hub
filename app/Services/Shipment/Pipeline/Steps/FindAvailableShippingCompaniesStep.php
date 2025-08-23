<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Models\ShippingCompany;
use Closure;

final class FindAvailableShippingCompaniesStep
{
    public function __invoke(array $data, Closure $next)
    {
        /** @var ShipmentPriceCalculationRequest $request */
        $request = $data['request'];

        $query = ShippingCompany::query()
            ->where('is_active', true)
            ->OperatesInRegion($request->recipientRegionId);

        if ($request->homePickup) {
            $query->whereNotNull('home_pickup_cost')->where('home_pickup_cost', '>', 0);
        }

        $data['shipping_companies'] = $query->get(['id', 'name', 'home_pickup_cost', 'local_tax_rate', 'insurance_type', 'insurance_value'])->keyBy('id');

        return $next($data);
    }
}
