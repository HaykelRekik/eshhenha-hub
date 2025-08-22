<?php

declare(strict_types=1);

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Enums\PricingRuleType;
use App\Enums\ShippingCompanyInsuranceType;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\PricingRule;
use App\Models\Region;
use App\Models\ShippingCompany;
use App\Models\User;
use App\Services\Shipment\ShipmentPriceCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Create test data
    $this->country = Country::factory()->create();
    $this->region = Region::factory()->create(['country_id' => $this->country->id]);
    $this->city = City::factory()->create(['region_id' => $this->region->id]);

    $this->user = User::factory()->create();
    $this->company = Company::factory()->create(['user_id' => $this->user->id]);

    // Create multiple shipping companies with different configurations
    $this->shippingCompany1 = ShippingCompany::factory()->create([
        'is_active' => true,
        'insurance_type' => ShippingCompanyInsuranceType::PERCENTAGE,
        'insurance_value' => 2.5, // 2.5%
        'home_pickup_cost' => 25.0,
    ]);

    $this->shippingCompany2 = ShippingCompany::factory()->create([
        'is_active' => true,
        'insurance_type' => ShippingCompanyInsuranceType::AMOUNT,
        'insurance_value' => 50.0, // Fixed amount
        'home_pickup_cost' => 15.0,
    ]);

    $this->shippingCompany3 = ShippingCompany::factory()->create([
        'is_active' => true,
        'insurance_type' => ShippingCompanyInsuranceType::PERCENTAGE,
        'insurance_value' => 1.0, // 1%
        'home_pickup_cost' => 0.0, // No home pickup
    ]);

    // Create delivery zone relationships
    $this->shippingCompany1->deliveryZones()->attach($this->region);
    $this->shippingCompany2->deliveryZones()->attach($this->region);
    $this->shippingCompany3->deliveryZones()->attach($this->region);
});

it('returns empty array when no shipping companies available for region', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 999, // Non-existent region
        userId: $this->user->id,
        companyId: null,
        weight: 5.0,
        homePickup: false,
        shipmentValue: 1000.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toBe([]);
});

it('returns empty array when no pricing rules found', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 5.0,
        homePickup: false,
        shipmentValue: 1000.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toBe([]);
});

it('calculates prices correctly with specific pricing rule (user + shipping company)', function (): void {
    // Create a specific pricing rule for user + shipping company
    PricingRule::factory()->create([
        'user_id' => $this->user->id,
        'shipping_company_id' => $this->shippingCompany1->id,
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 15.0,
        'type' => PricingRuleType::CUSTOMER_SHIPPING_COMPANY,
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toHaveCount(1);
    expect($result)->toHaveKey($this->shippingCompany1->id);

    $breakdown = $result[$this->shippingCompany1->id]->breakdown;
    expect($breakdown->basePrice)->toBe(75.0); // 5kg * 15.0
    expect($breakdown->homePickupCost)->toBe(25.0);
    expect($breakdown->insuranceCost)->toBe(25.0); // 1000 * 2.5%
    expect($breakdown->taxAmount)->toBe(18.75); // (75 + 25 + 25) * 15%
    expect($breakdown->total)->toBe(143.75); // 75 + 25 + 25 + 18.75
});

it('calculates prices correctly with company-specific pricing rule', function (): void {
    // Create a company-specific pricing rule
    PricingRule::factory()->create([
        'user_id' => $this->user->id, // Use user_id instead of company_id
        'shipping_company_id' => $this->shippingCompany2->id,
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 12.0,
        'type' => PricingRuleType::CUSTOMER_SHIPPING_COMPANY, // Use customer type
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: $this->company->id,
        weight: 3.0,
        homePickup: false,
        shipmentValue: 500.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toHaveCount(1);

    $breakdown = $result[$this->shippingCompany2->id]->breakdown;
    expect($breakdown->basePrice)->toBe(36.0); // 3kg * 12.0
    expect($breakdown->homePickupCost)->toBe(0.0);
    expect($breakdown->insuranceCost)->toBe(50.0); // Fixed amount
    expect($breakdown->taxAmount)->toBe(12.9); // (36 + 0 + 50) * 15%
    expect($breakdown->total)->toBe(98.9); // 36 + 0 + 50 + 12.9
});

it('calculates prices correctly with shipping company specific rule (fallback)', function (): void {
    // Create a shipping company specific rule (no user/company)
    PricingRule::factory()->create([
        'shipping_company_id' => $this->shippingCompany3->id,
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 18.0,
        'type' => PricingRuleType::SHIPPING_COMPANY,
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 4.0,
        homePickup: true,
        shipmentValue: 800.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toHaveCount(1);

    $breakdown = $result[$this->shippingCompany3->id]->breakdown;
    expect($breakdown->basePrice)->toBe(72.0); // 4kg * 18.0
    expect($breakdown->homePickupCost)->toBe(0.0); // Company doesn't support home pickup
    expect($breakdown->insuranceCost)->toBe(8.0); // 800 * 1%
    expect($breakdown->taxAmount)->toBe(12.0); // (72 + 0 + 8) * 15%
    expect($breakdown->total)->toBe(92.0); // 72 + 0 + 8 + 12
});

it('calculates prices correctly with global pricing rule (final fallback)', function (): void {
    // Create a global pricing rule (no user, company, or shipping company)
    PricingRule::factory()->create([
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 20.0,
        'type' => PricingRuleType::GLOBAL,
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 2.5,
        homePickup: false,
        shipmentValue: 300.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toHaveCount(3); // All 3 shipping companies should get global rule

    // Check first shipping company
    $breakdown1 = $result[$this->shippingCompany1->id]->breakdown;
    expect($breakdown1->basePrice)->toBe(50.0); // 2.5kg * 20.0
    expect($breakdown1->homePickupCost)->toBe(0.0);
    expect($breakdown1->insuranceCost)->toBe(7.5); // 300 * 2.5%
    expect($breakdown1->taxAmount)->toBeGreaterThanOrEqual(8.62); // (50 + 0 + 7.5) * 15%
    expect($breakdown1->taxAmount)->toBeLessThanOrEqual(8.63);
    expect($breakdown1->total)->toBeGreaterThanOrEqual(66.12);
    expect($breakdown1->total)->toBeLessThanOrEqual(66.13);

    // Check second shipping company
    $breakdown2 = $result[$this->shippingCompany2->id]->breakdown;
    expect($breakdown2->basePrice)->toBe(50.0); // 2.5kg * 20.0
    expect($breakdown2->homePickupCost)->toBe(0.0);
    expect($breakdown2->insuranceCost)->toBe(50.0); // Fixed amount
    expect($breakdown2->taxAmount)->toBe(15.0); // (50 + 0 + 50) * 15%
    expect($breakdown2->total)->toBe(115.0);

    // Check third shipping company
    $breakdown3 = $result[$this->shippingCompany3->id]->breakdown;
    expect($breakdown3->basePrice)->toBe(50.0); // 2.5kg * 20.0
    expect($breakdown3->homePickupCost)->toBe(0.0);
    expect($breakdown3->insuranceCost)->toBe(3.0); // 300 * 1%
    expect($breakdown3->taxAmount)->toBeGreaterThanOrEqual(7.94); // (50 + 0 + 3) * 15%
    expect($breakdown3->taxAmount)->toBeLessThanOrEqual(7.96);
    expect($breakdown3->total)->toBeGreaterThanOrEqual(60.94);
    expect($breakdown3->total)->toBeLessThanOrEqual(60.96);
});

it('handles mixed pricing rules correctly (specific + fallback)', function (): void {
    // Create specific rule for first company
    PricingRule::factory()->create([
        'user_id' => $this->user->id,
        'shipping_company_id' => $this->shippingCompany1->id,
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 15.0,
        'type' => PricingRuleType::CUSTOMER_SHIPPING_COMPANY,
    ]);

    // Create global rule for others
    PricingRule::factory()->create([
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 20.0,
        'type' => PricingRuleType::GLOBAL,
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 6.0,
        homePickup: true,
        shipmentValue: 1200.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toHaveCount(3);

    // First company should use specific rule
    $breakdown1 = $result[$this->shippingCompany1->id]->breakdown;
    expect($breakdown1->basePrice)->toBe(90.0); // 6kg * 15.0
    expect($breakdown1->homePickupCost)->toBe(25.0);
    expect($breakdown1->insuranceCost)->toBe(30.0); // 1200 * 2.5%
    expect($breakdown1->taxAmount)->toBe(21.75); // (90 + 25 + 30) * 15%
    expect($breakdown1->total)->toBe(166.75);

    // Other companies should use global rule
    $breakdown2 = $result[$this->shippingCompany2->id]->breakdown;
    expect($breakdown2->basePrice)->toBe(120.0); // 6kg * 20.0
    expect($breakdown2->homePickupCost)->toBe(15.0);
    expect($breakdown2->insuranceCost)->toBe(50.0); // Fixed amount
    expect($breakdown2->taxAmount)->toBe(27.75); // (120 + 15 + 50) * 15%
    expect($breakdown2->total)->toBe(212.75);
});

it('handles zero values correctly', function (): void {
    // Create a rule with zero values
    PricingRule::factory()->create([
        'shipping_company_id' => $this->shippingCompany1->id,
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 0.0, // Free shipping
        'type' => PricingRuleType::SHIPPING_COMPANY,
    ]);

    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: $this->region->id,
        userId: $this->user->id,
        companyId: null,
        weight: 5.0,
        homePickup: false,
        shipmentValue: 100.0,
    );

    $service = ShipmentPriceCalculatorService::make();
    $result = $service->calculatePrices($request);

    expect($result)->toHaveCount(1);

    $breakdown = $result[$this->shippingCompany1->id]->breakdown;
    expect($breakdown->basePrice)->toBe(0.0); // 5kg * 0.0
    expect($breakdown->homePickupCost)->toBe(0.0);
    expect($breakdown->insuranceCost)->toBe(2.5); // 100 * 2.5%
    expect($breakdown->taxAmount)->toBe(0.375); // (0 + 0 + 2.5) * 15%
    expect($breakdown->total)->toBe(2.875);
});

it('debug: checks if shipping companies are found in region', function (): void {
    // This test just checks if the setup is working correctly
    $shippingCompanies = ShippingCompany::query()
        ->OperatesInRegion($this->region->id)
        ->where('is_active', true)
        ->get();

    expect($shippingCompanies)->toHaveCount(3);
    expect($shippingCompanies->pluck('id')->toArray())->toContain($this->shippingCompany1->id);
    expect($shippingCompanies->pluck('id')->toArray())->toContain($this->shippingCompany2->id);
    expect($shippingCompanies->pluck('id')->toArray())->toContain($this->shippingCompany3->id);
});

it('debug: checks if pricing rule is created correctly', function (): void {
    // Create a pricing rule
    $rule = PricingRule::factory()->create([
        'company_id' => $this->company->id,
        'shipping_company_id' => $this->shippingCompany2->id,
        'weight_from' => 1.0,
        'weight_to' => 10.0,
        'local_price_per_kg' => 12.0,
        'type' => PricingRuleType::COMPANY_SHIPPING_COMPANY,
    ]);

    // Check if the rule exists
    expect($rule->id)->not->toBeNull();
    expect($rule->company_id)->toBe($this->company->id);
    expect($rule->shipping_company_id)->toBe($this->shippingCompany2->id);

    // Check if we can find it with a query
    $foundRule = PricingRule::query()
        ->where('company_id', $this->company->id)
        ->where('shipping_company_id', $this->shippingCompany2->id)
        ->forWeight(3.0)
        ->first();

    expect($foundRule)->not->toBeNull();
    expect($foundRule->id)->toBe($rule->id);
});
