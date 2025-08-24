<?php

declare(strict_types=1);

namespace App\Filament\Pages\User\Actions;

use App\DTOs\Payment\PaymentRequestDto;
use App\Enums\Icons\PhosphorIcons;
use App\Models\User;
use App\Services\Payment\MyFatoorahService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Auth\Authenticatable;

class TopupBalanceAction
{
    public static function make(): Action
    {
        return Action::make('topup_balance')
            ->label(__('Topup Balance'))
            ->icon(PhosphorIcons::CreditCardDuotone)
            ->outlined()
            ->modal()
            ->modalWidth(Width::Medium)
            ->schema([
                TextInput::make('amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->saudiRiyal(),
            ])
            ->action(function (array $data, MyFatoorahService $myFatoorahService) {
                /** @var User $user */
                $user = auth()->user();
                $paymentRequest = self::createPaymentRequest($data, $user);

                $response = $myFatoorahService->generatePaymentLink($paymentRequest);

                if ($response['success'] && isset($response['data']['invoiceURL'])) {
                    return redirect()->away($response['data']['invoiceURL']);
                }

                self::sendFailureNotification();
            });
    }

    private static function createPaymentRequest(array $data, Authenticatable $user): PaymentRequestDto
    {
        $amount = (float) $data['amount'];
        $note = self::buildPaymentNote($amount, $user);

        return new PaymentRequestDto(
            amount: $amount,
            customerEmail: $user->email,
            customerName: $user->name,
            callbackUrl: route('payment.callback'),
            displayCurrency: 'SAR',
            note: $note
        );
    }

    private static function buildPaymentNote(float $amount, Authenticatable $user): string
    {
        return json_encode([
            'operation' => 'balance topup',
            'date' => now(),
            'user' => $user->name,
            'user_id' => $user->id,
            'amount' => $amount,
        ]);
    }

    private static function sendFailureNotification(): void
    {
        Notification::make()
            ->danger()
            ->title(__('Failed to initialize payment'))
            ->body(__('Please try again later.'))
            ->send();
    }
}
