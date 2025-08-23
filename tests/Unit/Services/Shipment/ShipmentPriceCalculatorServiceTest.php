<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Shipment;

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Enums\PricingRuleType;
use App\Enums\ShippingCompanyInsuranceType;
use App\Models\Company;
use App\Models\PricingRule;
use App\Models\Region;
use App\Models\ShippingCompany;
use App\Models\User;
use App\Services\Shipment\ShipmentPriceCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Create a user, company, and two shipping companies
    $this->user = User::factory()->create();
    $this->company = Company::factory()->create();
    $this->region = Region::factory()->create();

    $this->shippingCompany1 = ShippingCompany::factory()->create([
        'is_active' => true,
        'home_pickup_cost' => 10,
        'insurance_type' => ShippingCompanyInsuranceType::PERCENTAGE,
        'insurance_value' => 2,
    ]);
    $this->shippingCompany2 = ShippingCompany::factory()->create([
        'is_active' => true,
        'home_pickup_cost' => 15,
        'insurance_type' => ShippingCompanyInsuranceType::AMOUNT,
        'insurance_value' => 25,
    ]);

    $this->shippingCompany1->deliveryZones()->attach($this->region->id);
    $this->shippingCompany2->deliveryZones()->attach($this->region->id);

    // Create pricing rules for various scenarios
    PricingRule::factory()->create([
        'type' => PricingRuleType::GLOBAL,
        'local_price_per_kg' => 10,
        'weight_from' => 1,
        'weight_to' => 10,
    ]);

    PricingRule::factory()->create([
        'type' => PricingRuleType::CUSTOMER,
        'user_id' => $this->user->id,
        'local_price_per_kg' => 9,
        'weight_from' => 1,
        'weight_to' => 10,
    ]);

    PricingRule::factory()->create([
        'type' => PricingRuleType::COMPANY,
        'company_id' => $this->company->id,
        'local_price_per_kg' => 8,
        'weight_from' => 1,
        'weight_to' => 10,
    ]);

    PricingRule::factory()->create([
        'type' => PricingRuleType::SHIPPING_COMPANY,
        'shipping_company_id' => $this->shippingCompany1->id,
        'local_price_per_kg' => 7,
        'weight_from' => 1,
        'weight_to' => 10,
    ]);

    PricingRule::factory()->create([
        'type' => PricingRuleType::CUSTOMER_SHIPPING_COMPANY,
        'user_id' => $this->user->id,
        'shipping_company_id' => $this->shippingCompany1->id,
        'local_price_per_kg' => 6,
        'weight_from' => 1,
        'weight_to' => 10,
    ]);

    PricingRule::factory()->create([
        'type' => PricingRuleType::COMPANY_SHIPPING_COMPANY,
        'company_id' => $this->company->id,
        'shipping_company_id' => $this->shippingCompany1->id,
        'local_price_per_kg' => 5,
        'weight_from' => 1,
        'weight_to' => 10,
    ]);
});

it('calculates price using global rule when no other rules match', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: null,
        companyId: null,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    expect($prices)->toBeArray()
        ->and(count($prices))->toBe(2)
        ->and($prices[$this->shippingCompany1->id]->breakdown->basePrice)->toBe(35.0) // SHIPPING_COMPANY rule
        ->and($prices[$this->shippingCompany2->id]->breakdown->basePrice)->toBe(50.0); // GLOBAL rule
});

it('calculates price using customer rule', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    expect($prices[$this->shippingCompany1->id]->breakdown->basePrice)->toBe(30.0) // CUSTOMER_SHIPPING_COMPANY rule
    ->and($prices[$this->shippingCompany2->id]->breakdown->basePrice)->toBe(45.0); // CUSTOMER rule
});

it('calculates price using company rule', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: null,
        companyId: $this->company->id,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    expect($prices[$this->shippingCompany1->id]->breakdown->basePrice)->toBe(25.0) // COMPANY_SHIPPING_COMPANY rule
    ->and($prices[$this->shippingCompany2->id]->breakdown->basePrice)->toBe(40.0); // COMPANY rule
});

it('calculates price using shipping company rule', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: null,
        companyId: null,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    // Only shipping company 1 has a specific rule
    expect($prices[$this->shippingCompany1->id]->breakdown->basePrice)->toBe(35.0) // 5kg * 7
    ->and($prices[$this->shippingCompany2->id]->breakdown->basePrice)->toBe(50.0); // Global rule
});

it('calculates price using customer and shipping company rule', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    // Only shipping company 1 has a specific rule for this customer
    expect($prices[$this->shippingCompany1->id]->breakdown->basePrice)->toBe(30.0) // 5kg * 6
    ->and($prices[$this->shippingCompany2->id]->breakdown->basePrice)->toBe(45.0); // Customer rule
});

it('calculates price using company and shipping company rule', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: null,
        companyId: $this->company->id,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    // Only shipping company 1 has a specific rule for this company
    expect($prices[$this->shippingCompany1->id]->breakdown->basePrice)->toBe(25.0) // 5kg * 5
    ->and($prices[$this->shippingCompany2->id]->breakdown->basePrice)->toBe(40.0); // Company rule
});

it('calculates price with home pickup cost', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: null,
        companyId: null,
        weight: 5,
        homePickup: true,
        shipmentValue: 100,
        insured: false
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    expect($prices[$this->shippingCompany1->id]->breakdown->homePickupCost)->toBe(10.0)
        ->and($prices[$this->shippingCompany2->id]->breakdown->homePickupCost)->toBe(15.0);
});

it('calculates price with insurance cost', function () {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: null,
        companyId: null,
        weight: 5,
        homePickup: false,
        shipmentValue: 100,
        insured: true
    );

    $service = new ShipmentPriceCalculatorService();
    $prices = $service->calculatePrices($request);

    expect($prices[$this->shippingCompany1->id]->breakdown->insuranceCost)->toBe(2.0) // 2% of 100
        ->and($prices[$this->shippingCompany2->id]->breakdown->insuranceCost)->toBe(25.0); // fixed amount
});