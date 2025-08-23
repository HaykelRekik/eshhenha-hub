<?php

declare(strict_types=1);

namespace App\Services\Shipping;

use App\Contracts\ShippingServiceInterface;

/**
 * Aramex shipping service implementation
 */
class AramexShippingService implements ShippingServiceInterface
{
    public function __construct(
        private string $baseUrl = '',
        private string $apiKey = ''
    ) {
        $this->baseUrl = config('shipping.dhl.base_url');
        $this->apiKey = config('shipping.dhl.api_key');
    }

    public function createShipment(array $data): array
    {
        return [];
    }

    public function trackShipment(string $trackingNumber): array
    {
        return [];
    }
}
