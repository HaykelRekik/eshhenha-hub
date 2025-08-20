<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\DTOs\Payment\PaymentRequestDto;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final readonly class MyFatoorahService
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function generatePaymentLink(PaymentRequestDto $dto): array
    {
        $response = Http::myFatoorah()
            ->post(url: '/SendPayment', data: $dto->toArray())
            ->throw()
            ->json();

        return [
            'success' => isset($response['Data']['InvoiceURL']),
            'data' => [
                'paymentId' => $response['Data']['InvoiceId'] ?? null,
                'invoiceURL' => $response['Data']['InvoiceURL'] ?? null,
            ],
        ];
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function checkPaymentStatus(string $paymentId, string $keyType = 'PaymentId'): array
    {
        return Http::myFatoorah()
            ->post('/GetPaymentStatus', [
                'Key' => $paymentId,
                'KeyType' => $keyType,
            ])
            ->throw()
            ->json();
    }

    /**
     * Request a refund for a payment
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function requestRefund(string $paymentId, float $amount): array
    {
        $response = Http::myFatoorah()
            ->post('/MakeRefund', [
                'Key' => $paymentId,
                'KeyType' => 'PaymentId',
                'Amount' => $amount,
                'Comment' => 'Store Rejected',
                'ServiceChargeOnCustomer' => false,
            ])
            ->throw()
            ->json();

        return [
            'success' => $response['IsSuccess'] ?? false,
            'data' => $response['Data'] ?? [],
            'refundId' => $response['Data']['RefundId'] ?? null,
        ];
    }

    /**
     * Check the status of a refund
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function checkRefundStatus(string $refundId): array
    {
        return Http::myFatoorah()
            ->post('/GetRefundStatus', [
                'Key' => $refundId,
                'KeyType' => 'RefundId',
            ])
            ->throw()
            ->json();
    }
}
