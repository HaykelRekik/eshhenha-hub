<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PricingRuleType;
use App\Models\Company;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PricingRule>
 */
class PricingRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PricingRule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'weight_from' => $this->faker->randomFloat(2, 0.1, 5.0),
            'weight_to' => $this->faker->randomFloat(2, 5.1, 20.0),
            'local_price_per_kg' => $this->faker->randomFloat(2, 5.0, 50.0),
            'international_price_per_kg' => $this->faker->randomFloat(2, 10.0, 100.0),
            'type' => $this->faker->randomElement(PricingRuleType::cases()),
        ];
    }

    /**
     * Indicate that the pricing rule is for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the pricing rule is for a specific company.
     */
    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $company->id,
        ]);
    }

    /**
     * Indicate that the pricing rule is for a specific shipping company.
     */
    public function forShippingCompany(ShippingCompany $shippingCompany): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_company_id' => $shippingCompany->id,
        ]);
    }

    /**
     * Indicate that the pricing rule is global (no specific associations).
     */
    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'company_id' => null,
            'shipping_company_id' => null,
            'type' => PricingRuleType::GLOBAL,
        ]);
    }
}
