<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use App\Services\Shipment\DTOs\ShipmentPriceCalculationRequest;

/**
 * Example usage of the ShipmentPriceCalculatorService
 *
 * This file demonstrates how to use the service in your application.
 * You can copy and adapt this code for your specific use case.
 */
class ExampleUsage
{
    public function calculateShippingPrices(): array
    {
        // Create the service using the factory method
        $service = ShipmentPriceCalculatorService::make();

        // Create a request for a shipment
        $request = new ShipmentPriceCalculationRequest(
            recipientRegionId: 1, // Region ID where the shipment will be delivered
            userId: 123, // User ID of the sender
            companyId: null, // Company ID if the user has a company, null otherwise
            weight: 5.0, // Weight in kilograms
            homePickup: true, // Whether home pickup is requested
            shipmentValue: 1000.0, // Value of the shipment for insurance calculation
        );

        // Calculate prices for all available shipping companies
        $results = $service->calculatePrices($request);

        return $results;
    }

    public function calculateShippingPricesForCompany(int $companyId): array
    {
        $service = ShipmentPriceCalculatorService::make();

        $request = new ShipmentPriceCalculationRequest(
            recipientRegionId: 2,
            userId: null, // No user ID when sending from company
            companyId: $companyId, // Company ID of the sender
            weight: 10.0,
            homePickup: false,
            shipmentValue: 2500.0,
        );

        return $service->calculatePrices($request);
    }

    public function processResults(array $results): void
    {
        if (empty($results)) {
            echo "No shipping options available for this shipment.\n";

            return;
        }

        echo "Available shipping options:\n";
        echo "==========================\n\n";

        foreach ($results as $shippingCompanyId => $option) {
            echo "Shipping Company: {$option->name}\n";
            echo "Base Price: {$option->breakdown->basePrice} SAR\n";
            echo "Home Pickup: {$option->breakdown->homePickupCost} SAR\n";
            echo "Insurance: {$option->breakdown->insuranceCost} SAR\n";
            echo "Tax: {$option->breakdown->taxAmount} SAR\n";
            echo "Total: {$option->breakdown->total} SAR\n";
            echo "---\n";
        }
    }
}
