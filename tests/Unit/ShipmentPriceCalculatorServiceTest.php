<?php

declare(strict_types=1);

use App\Services\Shipment\DTOs\PriceBreakdown;
use App\Services\Shipment\DTOs\ShipmentPriceCalculationRequest;
use App\Services\Shipment\DTOs\ShippingCompanyPriceBreakdown;
use App\Services\Shipment\PriceBreakdownCalculatorService;
use App\Services\Shipment\PricingRuleFinderService;
use App\Services\Shipment\ShipmentPriceCalculatorService;

it('can be instantiated with make method', function (): void {
    $service = ShipmentPriceCalculatorService::make();

    expect($service)->toBeInstanceOf(ShipmentPriceCalculatorService::class);
});

it('can be instantiated with constructor injection', function (): void {
    $pricingRuleFinder = new PricingRuleFinderService();
    $priceBreakdownCalculator = new PriceBreakdownCalculatorService();

    $service = new ShipmentPriceCalculatorService(
        pricingRuleFinder: $pricingRuleFinder,
        priceBreakdownCalculator: $priceBreakdownCalculator,
    );

    expect($service)->toBeInstanceOf(ShipmentPriceCalculatorService::class);
});

it('creates correct DTOs', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    expect($request->recipientRegionId)->toBe(1);
    expect($request->userId)->toBe(1);
    expect($request->companyId)->toBeNull();
    expect($request->weight)->toBe(5.0);
    expect($request->homePickup)->toBeTrue();
    expect($request->shipmentValue)->toBe(1000.0);
});

it('creates correct price breakdown DTO', function (): void {
    $breakdown = new PriceBreakdown(
        basePrice: 75.0,
        homePickupCost: 25.0,
        insuranceCost: 25.0,
        taxAmount: 18.75,
        total: 143.75,
    );

    expect($breakdown->basePrice)->toBe(75.0);
    expect($breakdown->homePickupCost)->toBe(25.0);
    expect($breakdown->insuranceCost)->toBe(25.0);
    expect($breakdown->taxAmount)->toBe(18.75);
    expect($breakdown->total)->toBe(143.75);
});

it('creates correct shipping company price breakdown DTO', function (): void {
    $breakdown = new PriceBreakdown(
        basePrice: 75.0,
        homePickupCost: 25.0,
        insuranceCost: 25.0,
        taxAmount: 18.75,
        total: 143.75,
    );

    $shippingCompanyBreakdown = new ShippingCompanyPriceBreakdown(
        name: 'Test Company',
        breakdown: $breakdown,
    );

    expect($shippingCompanyBreakdown->name)->toBe('Test Company');
    expect($shippingCompanyBreakdown->breakdown)->toBe($breakdown);
});

it('pricing rule finder can be instantiated', function (): void {
    $finder = new PricingRuleFinderService();

    expect($finder)->toBeInstanceOf(PricingRuleFinderService::class);
});

it('pricing rule finder has the expected methods', function (): void {
    $finder = new PricingRuleFinderService();

    expect(method_exists($finder, 'findApplicableRulesForCompanies'))->toBeTrue();
    expect(method_exists($finder, 'findApplicableRule'))->toBeTrue();
});
