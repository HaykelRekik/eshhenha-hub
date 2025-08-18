<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $region = Region::inRandomOrder()->first();
        $city = $region ? City::where('region_id', $region->id)->inRandomOrder()->first() : City::inRandomOrder()->first();
        $country = $region?->country ?? Country::inRandomOrder()->first();

        return [
            'label' => fake()->randomElement(['Home', 'Work', 'Main Office', 'Billing']),
            'contact_name' => fake()->name(),
            'contact_phone_number' => fake()->phoneNumber(),
            'street' => fake()->streetAddress(),
            'zip_code' => fake()->postcode(),
            'is_default' => fake()->boolean(25),
            'country_id' => $country?->id,
            'region_id' => $region?->id,
            'city_id' => $city?->id,
        ];
    }
}
