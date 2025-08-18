<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => fake()->text(50),
            'title_ar' => fake()->text(50),
            'title_en' => fake()->text(50),
            'title_ur' => fake()->text(50),
            'description_ar' => fake()->text(50),
            'description_en' => fake()->text(50),
            'description_ur' => fake()->text(50),
            'link' => fake()->text(50),

        ];
    }
}
