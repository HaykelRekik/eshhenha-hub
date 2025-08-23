<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LoyaltyConversionSettings extends Settings
{
    // Points to Balance conversion rules
    public int $points_to_balance_points;

    public float $points_to_balance_amount;

    public int $minimum_transferable_points;

    // Balance to Points conversion rules
    public float $balance_to_points_amount;

    public int $balance_to_points_corresponding_points;

    public float $minimum_amount_to_transfer;

    public static function group(): string
    {
        return 'loyalty_conversion';
    }

    public static function encrypted(): array
    {
        return [];
    }

    /**
     * Get the conversion rate from points to balance
     */
    public function getPointsToBalanceRate(): float
    {
        if (0 === $this->points_to_balance_points) {
            return 0;
        }

        return $this->points_to_balance_amount / $this->points_to_balance_points;
    }

    /**
     * Get the conversion rate from balance to points
     */
    public function getBalanceToPointsRate(): float
    {
        if (0 === $this->balance_to_points_amount) {
            return 0;
        }

        return $this->balance_to_points_corresponding_points / $this->balance_to_points_amount;
    }

    /**
     * Convert points to balance amount
     */
    public function convertPointsToBalance(int $points): float
    {
        return $points * $this->getPointsToBalanceRate();
    }

    /**
     * Convert balance amount to points
     */
    public function convertBalanceToPoints(float $amount): int
    {
        return (int) ($amount * $this->getBalanceToPointsRate());
    }

    /**
     * Check if points amount is transferable
     */
    public function canTransferPoints(int $points): bool
    {
        return $points >= $this->minimum_transferable_points;
    }

    /**
     * Check if balance amount is transferable
     */
    public function canTransferAmount(float $amount): bool
    {
        return $amount >= $this->minimum_amount_to_transfer;
    }
}
