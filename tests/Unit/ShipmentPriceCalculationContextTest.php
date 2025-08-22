<?php

declare(strict_types=1);

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\Services\Shipment\Pipeline\ShipmentPriceCalculationContext;

it('can create context from request', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);

    expect($context->request)->toBe($request);
    expect($context->availableShippingCompanies)->toBeEmpty();
    expect($context->pricingRules)->toBeEmpty();
    expect($context->results)->toBeEmpty();
});

it('can update available shipping companies', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);
    $companies = collect(['company1', 'company2']);

    $updatedContext = $context->withAvailableShippingCompanies($companies);

    expect($updatedContext->availableShippingCompanies)->toBe($companies);
    expect($updatedContext->pricingRules)->toBeEmpty();
    expect($updatedContext->results)->toBeEmpty();
});

it('can update pricing rules', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);
    $rules = collect(['rule1', 'rule2']);

    $updatedContext = $context->withPricingRules($rules);

    expect($updatedContext->pricingRules)->toBe($rules);
    expect($updatedContext->availableShippingCompanies)->toBeEmpty();
    expect($updatedContext->results)->toBeEmpty();
});

it('can update results', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);
    $results = ['result1', 'result2'];

    $updatedContext = $context->withResults($results);

    expect($updatedContext->results)->toBe($results);
    expect($updatedContext->availableShippingCompanies)->toBeEmpty();
    expect($updatedContext->pricingRules)->toBeEmpty();
});

it('can get request properties', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 2,
        companyId: 3,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);

    expect($context->getRecipientRegionId())->toBe(1);
    expect($context->getUserId())->toBe(2);
    expect($context->getCompanyId())->toBe(3);
    expect($context->getWeight())->toBe(5.0);
    expect($context->getHomePickup())->toBeTrue();
    expect($context->getShipmentValue())->toBe(1000.0);
});

it('can check if has available shipping companies', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);
    expect($context->hasAvailableShippingCompanies())->toBeFalse();

    $companies = collect(['company1']);
    $updatedContext = $context->withAvailableShippingCompanies($companies);
    expect($updatedContext->hasAvailableShippingCompanies())->toBeTrue();
});

it('can get shipping company IDs', function (): void {
    $request = new ShipmentPriceCalculationRequest(
        recipientRegionId: 1,
        userId: 1,
        companyId: null,
        weight: 5.0,
        homePickup: true,
        shipmentValue: 1000.0,
    );

    $context = ShipmentPriceCalculationContext::fromRequest($request);
    $companies = collect([
        (object) ['id' => 1],
        (object) ['id' => 2],
    ]);

    $updatedContext = $context->withAvailableShippingCompanies($companies);
    $ids = $updatedContext->getShippingCompanyIds();

    expect($ids)->toHaveCount(2);
    expect($ids->toArray())->toBe([1, 2]);
});
