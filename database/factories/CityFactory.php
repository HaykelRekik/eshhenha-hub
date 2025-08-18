<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_ar' => fake()->text(50),
            'name_en' => fake()->text(50),
            'name_ur' => fake()->text(50),
            'is_active' => fake()->text(50),

        ];
    }
}
