<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone_number' => fake()->unique()->numerify('+96650#######'),
            'national_id' => fake()->unique()->numerify('1##########'),
            'avatar_url' => null,
            'role' => UserRole::USER,
            'is_active' => true,
            'last_login_at' => fake()->dateTimeThisMonth(),
            'last_login_ip' => fake()->ipv4(),
            'referral_code' => Str::upper(Str::random(8)),
            'referred_by' => null,
            'email_verified_at' => now(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if ($user->company) {
                return;
            }

            Address::factory(random_int(1, 3))->create([
                'addressable_id' => $user->id,
                'addressable_type' => User::class,
            ]);
        });
    }

    /**
     * Indicate that the user has a company profile with warehouses.
     */
    public function withCompanyProfile(): static
    {
        return $this->afterCreating(function (User $user): void {
            Company::factory()
                ->for($user)
                ->has(Address::factory(), 'address')
                ->has(
                    Warehouse::factory()->count(2)
                        ->has(Address::factory(), 'address')
                )
                ->create();
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
