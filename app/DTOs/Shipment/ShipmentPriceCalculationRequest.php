<?php

declare(strict_types=1);

namespace App\Services\Shipment\DTOs;

final readonly class ShipmentPriceCalculationRequest
{
    public function __construct(
        public int $recipientRegionId,
        public ?int $userId,
        public ?int $companyId,
        public float $weight,
        public bool $homePickup,
        public float $shipmentValue,
    ) {}
}
