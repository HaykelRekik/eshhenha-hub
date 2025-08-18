<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_name' => fake()->firstName() . ' ' . fake()->lastName(),
            'sender_email' => fake()->unique()->safeEmail(),
            'sender_phone_number' => fake()->numerify('+96650#######'),
            'subject' => fake()->words(nb: random_int(1, 4), asText: true),
            'message' => fake()->paragraphs(nb: random_int(1, 2), asText: true),
        ];
    }
}
