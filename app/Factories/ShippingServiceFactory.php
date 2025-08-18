<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\ShippingServiceInterface;
use App\Services\Shipping\AramexShippingService;
use App\Services\Shipping\DHLShippingService;
use App\Services\Shipping\SMSAShippingService;
use InvalidArgumentException;

class ShippingServiceFactory
{
    /**
     * Create shipping service instance based on provider
     */
    public static function create(string $provider): ShippingServiceInterface
    {

        return match (mb_strtolower($provider)) {
            'smsa' => new SMSAShippingService(),
            'dhl' => new DHLShippingService(),
            'aramex' => new AramexShippingService(),
            default => throw new InvalidArgumentException("Unsupported shipping provider: {$provider}")
        };
    }
}
