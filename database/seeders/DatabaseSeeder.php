<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Company;
use App\Models\Region;
use App\Models\ShippingCompany;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                AdminSeeder::class,
                CountrySeeder::class,
                RegionSeeder::class,
                CitySeeder::class,
            ]
        );

        if (app()->environment('local')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            User::truncate();
            Company::truncate();
            ShippingCompany::truncate();
            Address::truncate();
            Warehouse::truncate();
            DB::table('contracts')->truncate();
            DB::table('shipping_company_delivery_zones')->truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $smsa = ShippingCompany::factory()
                ->has(Address::factory(), 'address')
                ->create(['name' => 'SMSA Express', 'logo' => '/logos/smsa.png']);

            $dhl = ShippingCompany::factory()
                ->has(Address::factory(), 'address')
                ->create(['name' => 'DHL', 'logo' => '/logos/dhl.png']);

            $ups = ShippingCompany::factory()
                ->has(Address::factory(), 'address')
                ->create(['name' => 'UPS', 'logo' => '/logos/ups.png']);

            $regions = Region::inRandomOrder()->limit(5)->get();
            if ($regions->isNotEmpty()) {
                $smsa->deliveryZones()->attach($regions->pluck('id'));
                $dhl->deliveryZones()->attach($regions->pluck('id'));
            }
            User::factory()->count(10)->create(['role' => UserRole::USER]);

            $companyUsers = User::factory()->count(7)
                ->withCompanyProfile()
                ->create(['role' => UserRole::COMPANY]);

            $shippingCompanies = ShippingCompany::all();
            $companies = Company::all();

            if ($companies->isNotEmpty() && $shippingCompanies->isNotEmpty()) {
                foreach ($companies as $company) {
                    $company->contracts()->attach(
                        $shippingCompanies->random(random_int(1, 3))->pluck('id')->toArray()
                    );
                }
            }

            $this->call([
                AdminSeeder::class,
                ContactMessageSeeder::class,
            ]);

        }
    }
}
