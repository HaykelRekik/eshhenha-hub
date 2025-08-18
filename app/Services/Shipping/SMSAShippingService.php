<?php

declare(strict_types=1);

namespace App\Services\Shipping;

use App\Contracts\ShippingServiceInterface;

use App\DTOs\ShippingCompanies\SMSAShipmentDTO;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMSA Express shipping service implementation
 */
class SMSAShippingService implements ShippingServiceInterface
{
    public function __construct(
        private string $baseUrl = '',
        private string $username = '',
        private string $password = ''
    ) {
        $this->baseUrl = config('shipping.smsa.base_url');
        $this->username = config('shipping.smsa.username');
        $this->password = config('shipping.smsa.password');
    }

    public function createShipment(array $data): array
    {
        try {
            $dto = new SMSAShipmentDTO($data);
            $response = $this->makeRequest('/addShip', $dto->toArray());

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'success' => true,
                    'tracking_number' => $responseData['awbNo'] ?? null,
                    'reference' => $responseData['refNo'] ?? null,
                    'response' => $responseData,
                ];
            }

            return $this->handleErrorResponse($response);

        } catch (Exception $e) {
            return $this->handleException($e, 'Shipment Creation');
        }
    }

    public function trackShipment(string $trackingNumber): array
    {
        try {
            $payload = [
                'awbNo' => $trackingNumber,
                'passKey' => config('shipping.smsa.pass_key'),
            ];

            $response = $this->makeRequest('/getTracking', $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'success' => true,
                    'status' => $responseData['status'] ?? 'unknown',
                    'location' => $responseData['location'] ?? '',
                    'events' => $responseData['trackingEvents'] ?? [],
                    'response' => $responseData,
                ];
            }

            return $this->handleErrorResponse($response);

        } catch (Exception $e) {
            return $this->handleException($e, 'Tracking');
        }
    }

    /**
     * Make HTTP request to SMSA API
     */
    private function makeRequest(string $endpoint, array $payload): Response
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->baseUrl . $endpoint, $payload);
    }

    /**
     * Handle API error response
     */
    private function handleErrorResponse(Response $response): array
    {
        return [
            'success' => false,
            'error' => $response->json()['error'] ?? 'Request failed',
            'response' => $response->json(),
        ];
    }

    /**
     * Handle exception with logging
     */
    private function handleException(Exception $e, string $operation): array
    {
        Log::error("SMSA {$operation} Error: " . $e->getMessage());

        return [
            'success' => false,
            'error' => 'Service temporarily unavailable',
        ];
    }
}
