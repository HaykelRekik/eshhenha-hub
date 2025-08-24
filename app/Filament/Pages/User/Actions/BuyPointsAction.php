<?php

declare(strict_types=1);

namespace App\Filament\Pages\User\Actions;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\WalletTransactionType;
use App\Models\User;
use App\Services\Wallet\WalletTransactionService;
use App\Settings\LoyaltyConversionSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class BuyPointsAction
{
    public static function make(): Action
    {
        return Action::make('buy_points')
            ->label(__('Recharge Loyalty Points'))
            ->icon(PhosphorIcons::Medal)
            ->color(Color::Rose)
            ->outlined()
            ->modal()
            ->modalWidth(Width::Large)
            ->schema(fn (LoyaltyConversionSettings $loyaltyConversionSettings): array => [
                Fieldset::make()
                    ->label(__('Conversion rules'))
                    ->columns(3)
                    ->components([
                        TextEntry::make('amount')
                            ->label(__('Amount'))
                            ->state($loyaltyConversionSettings->balance_to_points_amount)
                            ->saudiRiyal(),

                        TextEntry::make('points')
                            ->label(__('Equivalent points'))
                            ->suffix(' ' . __('point'))
                            ->state($loyaltyConversionSettings->balance_to_points_corresponding_points),

                        TextEntry::make('min')
                            ->label(__('Minimum Amount'))
                            ->state($loyaltyConversionSettings->minimum_amount_to_transfer)
                            ->saudiRiyal(),
                    ]),

                TextInput::make('amount')
                    ->label(__('Amount to transfer'))
                    ->numeric()
                    ->minValue($loyaltyConversionSettings?->minimum_amount_to_transfer ?? 1)
                    ->required()
                    ->live(debounce: 300)
                    ->partiallyRenderAfterStateUpdated()
                    ->afterLabel(
                        function ($state) use ($loyaltyConversionSettings): string {
                            if ($state && $state >= $loyaltyConversionSettings->minimum_amount_to_transfer) {
                                $equivalentPoints = $loyaltyConversionSettings->convertBalanceToPoints($state);

                                return __('This amount will be equivalent to :points point.', ['points' => $equivalentPoints]);
                            }

                            return '';
                        }
                    )
                    ->saudiRiyal(),
            ])
            ->action(function (array $data, LoyaltyConversionSettings $settings, WalletTransactionService $walletTxService): void {
                /** @var User $user */
                $user = auth()->user()->load('wallet');
                $wallet = $user->wallet;

                if ( ! $wallet) {
                    self::notify(
                        message: __('Wallet not found.'),
                    );

                    return;
                }

                if ($wallet->is_locked) {

                    self::notify(
                        message: __('Wallet is locked.'),
                        body: __('Please contact the administration.')
                    );

                    return;
                }

                $amount = (float) ($data['amount'] ?? 0);
                if ($amount <= 0) {
                    self::notify(
                        message: __('Invalid amount.'),
                    );

                    return;
                }

                $rate = $settings->getBalanceToPointsRate();
                if ($rate <= 0) {
                    self::notify(
                        message: __('Conversion rate is not configured.'),
                    );

                    return;
                }

                $requiredAmount = round($amount, 2);

                if ( ! $settings->canTransferAmount($requiredAmount)) {

                    self::notify(
                        message: __('Amount below minimum.'),
                        body: __('The minimum amount to convert is :amount SAR.', ['amount' => $settings->minimum_amount_to_transfer])
                    );

                    return;
                }

                if ($wallet->balance < $requiredAmount) {
                    self::notify(
                        message: __('Insufficient wallet balance.'),
                    );

                    return;
                }

                $points = $settings->convertBalanceToPoints($requiredAmount);
                if ($points <= 0) {
                    self::notify(
                        message: __('Calculated points are zero. Try a higher amount.'),
                    );

                    return;
                }

                DB::transaction(function () use ($wallet, $walletTxService, $requiredAmount, $points, $user): void {
                    $walletTxService->createTransaction(
                        wallet: $wallet,
                        type: WalletTransactionType::BUY_LOYALTY_POINTS,
                        amount: $requiredAmount,
                        reason: sprintf('Buy loyalty points (%d pts)', $points),
                        metadata: [
                            'operation' => 'LOYALTY_CONVERSION',
                            'points_purchased' => $points,
                            'amount_charged' => $requiredAmount,
                        ],
                    );

                    $user->increment('loyalty_points', $points);
                });

                self::notify(
                    message: __('Congratulations !'),
                    body: __('Points purchased successfully.'),
                    type: 'success'
                );
            });
    }

    private static function notify(string $message, ?string $body = null, string $type = 'danger'): void
    {
        Notification::make()
            ->{$type}()
            ->title($message)
            ->body($body)
            ->send();
    }
}
