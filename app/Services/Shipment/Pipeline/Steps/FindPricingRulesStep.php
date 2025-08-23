<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline\Steps;

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Models\PricingRule;
use Closure;

final class FindPricingRulesStep
{
    public function __invoke(array $data, Closure $next)
    {
        /** @var ShipmentPriceCalculationRequest $request */
        $request = $data['request'];

        $data['pricing_rules'] = PricingRule::query()
            ->forWeight($request->weight)
            ->get();

        return $next($data);
    }
}