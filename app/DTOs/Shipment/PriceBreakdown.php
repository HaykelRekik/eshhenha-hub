<?php

declare(strict_types=1);

namespace App\DTOs\Shipment;

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
