<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RewardSettings extends Settings
{
    public int $referrer_user_points;

    public int $referred_user_points;

    public int $welcome_bonus;

    public static function group(): string
    {
        return 'rewards';
    }
}
