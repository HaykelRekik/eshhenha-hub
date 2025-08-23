<?php

declare(strict_types=1);

use App\DTOs\Shipment\ShippingCompanyPriceBreakdown;
use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Models\Company;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use App\Models\User;
use App\Services\Shipment\ShipmentPriceCalculatorService;

it('calculates shipment prices for available shipping companies', function (): void {
    $user = User::factory()->create();
    $company = Company::factory()->create();
    $shippingCompany1 = ShippingCompany::factory()->create(['is_active' => true]);
    $shippingCompany2 = ShippingCompany::factory()->create(['is_active' => true]);

    // Create pricing rules for each shipping company
    PricingRule::factory()->create([
        'user_id' => $user->id,
        'shipping_company_id' => $shippingCompany1->id,
        'weight_from' => 1,
        'weight_to' => 10,
        'type' => 'customer',
        'local_price_per_kg' => 10,
        'international_price_per_kg' => 20,
    ]);
    PricingRule::factory()->create([
        'company_id' => $company->id,
        'shipping_company_id' => $shippingCompany2->id,
        'weight_from' => 1,
        'weight_to' => 10,
        'type' => 'company',
        'local_price_per_kg' => 15,
        'international_price_per_kg' => 25,
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: $user->id,
        companyId: $company->id,
        weight: 5.0,
        homePickup: false,
        shipmentValue: 100.0,
        insured: true
    );

    $service = app(ShipmentPriceCalculatorService::class);
    $results = $service->calculatePrices($request);

    expect($results)->not->toBeEmpty();
    expect($results)->toHaveCount(2);
    foreach ($results as $breakdown) {
        expect($breakdown)->toBeInstanceOf(ShippingCompanyPriceBreakdown::class);
        expect($breakdown->breakdown->total)->toBeGreaterThan(0);
    }
});
