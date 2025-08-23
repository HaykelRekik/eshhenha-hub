<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PricingRuleType;
use App\Models\Company;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use App\Models\User;
use Illuminate\Database\Seeder;

class PricingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $shippingCompany = ShippingCompany::factory()->create();

        $intervals = [
            ['weight_from' => 0, 'weight_to' => 5],
            ['weight_from' => 5.01, 'weight_to' => 10],
            ['weight_from' => 10.01, 'weight_to' => 20],
            ['weight_from' => 20.01, 'weight_to' => 50],
        ];

        foreach (PricingRuleType::cases() as $type) {
            foreach ($intervals as $interval) {
                $factory = PricingRule::factory()->state($interval);

                switch ($type) {
                    case PricingRuleType::GLOBAL:
                        $factory->global()->create(['type' => $type]);
                        break;
                    case PricingRuleType::CUSTOMER:
                        $factory->forUser($user)->create(['type' => $type]);
                        break;
                    case PricingRuleType::COMPANY:
                        $factory->forCompany($company)->create(['type' => $type]);
                        break;
                    case PricingRuleType::SHIPPING_COMPANY:
                        $factory->forShippingCompany($shippingCompany)->create(['type' => $type]);
                        break;
                    case PricingRuleType::CUSTOMER_SHIPPING_COMPANY:
                        $factory->forUser($user)->forShippingCompany($shippingCompany)->create(['type' => $type]);
                        break;
                    case PricingRuleType::COMPANY_SHIPPING_COMPANY:
                        $factory->forCompany($company)->forShippingCompany($shippingCompany)->create(['type' => $type]);
                        break;
                }
            }
        }
    }
}
