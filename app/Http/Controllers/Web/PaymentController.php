<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\WalletTransactionType;
use App\Filament\Pages\User\Wallet;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Payment\MyFatoorahService;
use App\Services\Wallet\WalletTransactionService;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function handleCallback(
        Request $request,
        MyFatoorahService $myFatoorah,
        WalletTransactionService $walletTxService,
    ): RedirectResponse {
        $paymentId = $this->getPaymentId($request);
        if (null === $paymentId || '' === $paymentId || '0' === $paymentId) {
            return $this->notifyAndRedirect(message: __('Missing payment identifier.'));
        }

        try {
            $status = $myFatoorah->checkPaymentStatus(paymentId: $paymentId);
        } catch (Throwable $e) {
            Log::error('MyFatoorah status error', ['error' => $e->getMessage()]);

            return $this->notifyAndRedirect(message: __('Unable to verify payment at the moment.'));
        }

        if ( ! $this->isPaymentSuccessful($status)) {
            return $this->notifyAndRedirect(
                message: __('Payment not successful.'),
                body: __('Please check your card information and expiration date, and make sure you have sufficient balance.')
            );
        }

        $context = $this->extractContext($status);
        $userId = (int) ($context['user_id'] ?? 0);
        $amount = (float) ($context['amount'] ?? 0);

        if ($userId <= 0 || $amount <= 0) {
            return $this->notifyAndRedirect(message: __('Invalid payment context.'), type: 'danger', routeName: 'filament.admin.pages.dashboard');
        }

        $user = $this->findUser($userId);
        if ( ! $user instanceof User) {
            return $this->notifyAndRedirect(message: __('User or wallet not found.'));
        }

        try {
            $this->processWalletTopUp($user, $amount, $walletTxService, $paymentId, $status);
        } catch (Exception $e) {
            Log::error('Wallet top-up transaction failed', ['error' => $e->getMessage()]);

            return $this->notifyAndRedirect(message: __('Failed to update wallet.'));
        }

        return $this->notifyAndRedirect(message: __('Wallet recharged successfully.'), type: 'success');
    }

    private function getPaymentId(Request $request): ?string
    {
        return $request->get('paymentId') ?? null;
    }

    private function isPaymentSuccessful(array $status): bool
    {
        $isSuccess = (bool) Arr::get($status, 'IsSuccess', false);
        $invoiceStatus = (string) Arr::get($status, 'Data.InvoiceStatus', '');

        return $isSuccess && in_array($invoiceStatus, ['Paid', 'Success'], true);
    }

    private function extractContext(array $status): array
    {
        $userDefined = (string) Arr::get($status, 'Data.UserDefinedField', '');

        if ('' === $userDefined) {
            return [];
        }

        try {
            return json_decode($userDefined, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return [];
        }
    }

    private function findUser(int $userId): ?User
    {
        return User::query()->with('wallet')->find($userId);
    }

    private function processWalletTopUp(
        User $user,
        float $amount,
        WalletTransactionService $walletTxService,
        string $paymentId,
        array $status
    ): void {
        DB::transaction(function () use ($user, $amount, $walletTxService, $paymentId, $status): void {
            /** @var \App\Models\Wallet $wallet */
            $wallet = $user->wallet;

            $walletTxService->createTransaction(
                wallet: $wallet,
                type: WalletTransactionType::DEPOSIT,
                amount: $amount,
                reason: 'MyFatoorah top-up',
                metadata: [
                    'gateway' => 'myfatoorah',
                    'payment_id' => $paymentId,
                    'status' => Arr::get($status, 'Data.InvoiceStatus'),
                ],
                external_identifier: $paymentId,
            );
        });
    }

    private function notifyAndRedirect(string $message, ?string $body = null, string $type = 'danger', ?string $routeName = null): RedirectResponse
    {
        Notification::make()
            ->{$type}()
            ->title($message)
            ->body($body)
            ->send();

        $redirectUrl = null !== $routeName && '' !== $routeName && '0' !== $routeName ? route($routeName) : Wallet::getUrl();

        return redirect()->to($redirectUrl);
    }
}
