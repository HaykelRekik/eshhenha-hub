<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Base class for shipping data transfer objects
 */
abstract class BaseShippingDTO
{
    public function __construct(
        protected readonly array $data
    ) {}

    /**
     * Convert DTO to array format required by shipping provider API
     */
    abstract public function toArray(): array;
}
