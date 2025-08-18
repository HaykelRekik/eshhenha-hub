<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ShippingCompanyInsuranceType;
use App\Enums\ShippingRange;
use App\Models\ShippingCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingCompany>
 */
class ShippingCompanyFactory extends Factory
{
    protected $model = ShippingCompany::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Express',
            'email' => fake()->unique()->companyEmail(),
            'logo' => fake()->imageUrl(200, 200, 'transport'),
            'phone_number' => fake()->phoneNumber(),
            'is_active' => true,
            'insurance_type' => fake()->randomElement(ShippingCompanyInsuranceType::cases()),
            'insurance_value' => fake()->randomFloat(2, 50, 500),
            'bank_code' => fake()->bothify('??##'),
            'bank_account_number' => fake()->iban(),
            'iban' => fake()->iban(),
            'swift' => fake()->swiftBicNumber(),
            'shipping_range' => fake()->randomElement(ShippingRange::cases()),
            'home_pickup_cost' => fake()->randomElement([null, 25.00, 30.00, 50.00]),
        ];
    }
}
