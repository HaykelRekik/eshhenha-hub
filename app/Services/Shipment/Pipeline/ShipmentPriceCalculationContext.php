<?php

declare(strict_types=1);

namespace App\Services\Shipment\Pipeline;

use App\DTOs\Shipment\ShipmentPriceCalculationRequest;
use App\DTOs\Shipment\ShippingCompanyPriceBreakdown;
use App\Models\PricingRule;
use App\Models\ShippingCompany;
use Illuminate\Support\Collection;

/**
 * Context class that holds all data needed throughout the shipment price calculation pipeline
 */
final readonly class ShipmentPriceCalculationContext
{
    /**
     * @param  ShipmentPriceCalculationRequest  $request  The original calculation request
     * @param  Collection<int, ShippingCompany>  $availableShippingCompanies  Available shipping companies for the region
     * @param  Collection<int, PricingRule>  $pricingRules  Applicable pricing rules for all companies
     * @param  array<int, ShippingCompanyPriceBreakdown>  $results  Calculated price breakdowns
     */
    public function __construct(
        public ShipmentPriceCalculationRequest $request,
        public Collection $availableShippingCompanies = new Collection(),
        public Collection $pricingRules = new Collection(),
        public array $results = []
    ) {}

    /**
     * Create a new context with the given request
     */
    public static function fromRequest(ShipmentPriceCalculationRequest $request): self
    {
        return new self(request: $request);
    }

    /**
     * Update the available shipping companies
     */
    public function withAvailableShippingCompanies(Collection $companies): self
    {
        return new self(
            request: $this->request,
            availableShippingCompanies: $companies,
            pricingRules: $this->pricingRules,
            results: $this->results
        );
    }

    /**
     * Update the pricing rules
     */
    public function withPricingRules(Collection $rules): self
    {
        return new self(
            request: $this->request,
            availableShippingCompanies: $this->availableShippingCompanies,
            pricingRules: $rules,
            results: $this->results
        );
    }

    /**
     * Update the results
     */
    public function withResults(array $results): self
    {
        return new self(
            request: $this->request,
            availableShippingCompanies: $this->availableShippingCompanies,
            pricingRules: $this->pricingRules,
            results: $results
        );
    }

    /**
     * Get the recipient region ID from the request
     */
    public function getRecipientRegionId(): int
    {
        return $this->request->recipientRegionId;
    }

    /**
     * Get the user ID from the request
     */
    public function getUserId(): ?int
    {
        return $this->request->userId;
    }

    /**
     * Get the company ID from the request
     */
    public function getCompanyId(): ?int
    {
        return $this->request->companyId;
    }

    /**
     * Get the weight from the request
     */
    public function getWeight(): float
    {
        return $this->request->weight;
    }

    /**
     * Get the home pickup flag from the request
     */
    public function getHomePickup(): bool
    {
        return $this->request->homePickup;
    }

    /**
     * Get the shipment value from the request
     */
    public function getShipmentValue(): float
    {
        return $this->request->shipmentValue;
    }

    /**
     * Check if there are available shipping companies
     */
    public function hasAvailableShippingCompanies(): bool
    {
        return $this->availableShippingCompanies->isNotEmpty();
    }

    /**
     * Check if there are pricing rules
     */
    public function hasPricingRules(): bool
    {
        return $this->pricingRules->isNotEmpty();
    }

    /**
     * Get shipping company IDs as a collection
     */
    public function getShippingCompanyIds(): Collection
    {
        return $this->availableShippingCompanies->pluck('id');
    }

    /**
     * Get the final results
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
