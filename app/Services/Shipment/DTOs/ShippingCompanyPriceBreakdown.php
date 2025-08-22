<?php

declare(strict_types=1);

namespace App\Services\Shipment\DTOs;

final readonly class ShippingCompanyPriceBreakdown
{
    public function __construct(
        public string $name,
        public PriceBreakdown $breakdown,
    ) {}
}
