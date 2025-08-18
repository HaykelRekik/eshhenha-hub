<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->text(50),
            'description' => fake()->text(50),
            'response' => fake()->text(50),
            'status' => fake()->text(50),

        ];
    }
}
