<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('loyalty_conversion.points_to_balance_points', 100);
        $this->migrator->add('loyalty_conversion.points_to_balance_amount', 10.00);
        $this->migrator->add('loyalty_conversion.minimum_transferable_points', 50);

        $this->migrator->add('loyalty_conversion.balance_to_points_amount', 1.00);
        $this->migrator->add('loyalty_conversion.balance_to_points_corresponding_points', 10);
        $this->migrator->add('loyalty_conversion.minimum_amount_to_transfer', 5.00);
    }

    public function down(): void
    {
        $this->migrator->delete('loyalty_conversion.points_to_balance_points');
        $this->migrator->delete('loyalty_conversion.points_to_balance_amount');
        $this->migrator->delete('loyalty_conversion.minimum_transferable_points');

        $this->migrator->delete('loyalty_conversion.balance_to_points_amount');
        $this->migrator->delete('loyalty_conversion.balance_to_points_corresponding_points');
        $this->migrator->delete('loyalty_conversion.minimum_amount_to_transfer');
    }
};
