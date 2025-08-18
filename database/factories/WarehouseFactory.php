<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city() . ' Warehouse',
            'responsible_name' => fake()->name(),
            'responsible_phone_number' => fake()->phoneNumber(),
            'responsible_email' => fake()->safeEmail(),
            'company_id' => Company::factory(),

        ];
    }
}
