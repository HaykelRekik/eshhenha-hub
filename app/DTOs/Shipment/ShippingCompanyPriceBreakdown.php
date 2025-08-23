<?php

declare(strict_types=1);

namespace App\DTOs\Shipment;

final readonly class ShippingCompanyPriceBreakdown
{
    public function __construct(
        public string $name,
        public PriceBreakdown $breakdown,
    ) {}
}
