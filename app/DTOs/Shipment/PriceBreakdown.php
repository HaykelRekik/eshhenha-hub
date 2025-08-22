<?php

declare(strict_types=1);

namespace App\Services\Shipment\DTOs;

final readonly class PriceBreakdown
{
    public function __construct(
        public float $basePrice,
        public float $homePickupCost,
        public float $insuranceCost,
        public float $taxAmount,
        public float $total,
    ) {}
}
