# Shipment Price Calculator Service

This service calculates shipping prices for multiple shipping companies based on various factors including pricing rules, home pickup, insurance, and tax calculations.

## Overview

The `ShipmentPriceCalculatorService` follows the Single Responsibility Principle and is composed of three main services:

1. **ShipmentPriceCalculatorService** - Main orchestrator service
2. **PricingRuleFinderService** - Finds applicable pricing rules based on priority using optimized batch queries
3. **PriceBreakdownCalculatorService** - Calculates detailed price breakdowns

## Performance Optimization

The service is optimized to avoid N+1 query problems by:
- Using a single query with `whereIn` to fetch all applicable pricing rules for multiple shipping companies
- Implementing a priority-based fallback system that efficiently handles rule discovery
- Creating a lookup map for fast access to pricing rules by shipping company ID

## Usage

### Basic Usage

```php
use App\Services\Shipment\ShipmentPriceCalculatorService;
use App\Services\Shipment\DTOs\ShipmentPriceCalculationRequest;

// Create the service
$service = ShipmentPriceCalculatorService::make();

// Create a request
$request = new ShipmentPriceCalculationRequest(
    recipientRegionId: 1,
    userId: 123,
    companyId: null, // or company ID if user has a company
    weight: 5.0, // in kg
    homePickup: true,
    shipmentValue: 1000.0, // for insurance calculation
);

// Calculate prices
$results = $service->calculatePrices($request);
```

### Return Structure

The service returns an array with shipping company IDs as keys:

```php
[
    1 => [
        'name' => 'Shipping Company Name',
        'breakdown' => [
            'basePrice' => 75.0,
            'homePickupCost' => 25.0,
            'insuranceCost' => 25.0,
            'taxAmount' => 18.75,
            'total' => 143.75,
        ]
    ]
]
```

## Pricing Rule Priority

The service searches for pricing rules in the following order using optimized batch queries:

1. **Specific Rule**: Combination of shipping company + user/company + weight range
2. **User/Company Specific**: Rules for user_id OR company_id only + weight range
3. **Shipping Company Specific**: Rules for shipping_company_id only + weight range
4. **Global Fallback**: Rules where all IDs are null + weight range

## Price Calculation Components

- **Base Price**: Based on weight and applicable pricing rule
- **Home Pickup**: Added if selected and shipping company supports it
- **Insurance**: Calculated based on shipment value and company's insurance type (percentage or fixed amount)
- **Tax**: 15% for local shipments (default), 0% for international

## Dependencies

- `PricingRuleFinderService` - Handles pricing rule discovery with batch queries
- `PriceBreakdownCalculatorService` - Handles price calculations
- `ShipmentPriceCalculationRequest` - Request DTO
- `ShippingCompanyPriceBreakdown` - Response DTO
- `PriceBreakdown` - Price breakdown DTO

## Error Handling

- Returns empty array if no shipping companies available for the region
- Returns empty array if no applicable pricing rules found
- Only returns shipping companies with valid pricing rules

## Performance Benefits

- **Before**: N+1 queries (1 query for shipping companies + N queries for pricing rules)
- **After**: 2 queries total (1 for shipping companies + 1 batch query for all pricing rules)
- **Scalability**: Performance remains consistent regardless of the number of shipping companies
