<?php

declare(strict_types=1);

namespace App\DTOs\Payment;

readonly class PaymentRequestDto
{
    public function __construct(
        public float $amount,
        public string $customerEmail,
        public string $customerName,
        public string $callbackUrl,
        public string $displayCurrency = 'SAR',
        public ?string $note = null,
    ) {}

    public function toArray(): array
    {
        return [
            'NotificationOption' => 'LNK',
            'CustomerName' => $this->customerName,
            'DisplayCurrencyIso' => $this->displayCurrency,
            'CustomerEmail' => $this->customerEmail,
            'InvoiceValue' => $this->amount,
            'CallBackUrl' => $this->callbackUrl ?? route('payment.callback'),
            'ErrorUrl' => $this->callbackUrl,
            'Language' => app()->getLocale(),
            'CustomerReference' => uniqid(prefix: 'ref_'),
            'UserDefinedField' => $this->note ?? '',
        ];
    }
}
