<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface for shipping service implementations
 */
interface ShippingServiceInterface
{
    /**
     * Create a new shipment
     */
    public function createShipment(array $data): array;

    /**
     * Track an existing shipment
     */
    public function trackShipment(string $trackingNumber): array;
}
