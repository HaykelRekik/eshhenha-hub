<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('rewards.referrer_user_points', 0);
        $this->migrator->add('rewards.referred_user_points', 0);
        $this->migrator->add('rewards.welcome_bonus', 0);
    }
};
