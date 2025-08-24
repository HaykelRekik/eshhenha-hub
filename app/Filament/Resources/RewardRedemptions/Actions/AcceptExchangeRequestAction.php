<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Actions;

use App\Enums\RewardRedemptionStatus;
use App\Models\RewardRedemption;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class AcceptExchangeRequestAction
{
    public static function make(): Action
    {
        return Action::make('acceptExchangeRequest')
            ->label(__('Accept Request'))
            ->color('success')
            ->outlined()
            ->icon('heroicon-o-check-circle')
            ->modalSubmitActionLabel(__('Accept'))
            ->modalHeading(__('Accept Exchange Request'))
            ->modalWidth(Width::Large)
            ->visible(fn (RewardRedemption $redemption): bool => $redemption->user->loyalty_points >= $redemption->reward->required_points && RewardRedemptionStatus::NEW === $redemption->status)
            ->schema([
                Textarea::make('redemption_instructions')
                    ->label(__('Redemption Instructions'))
                    ->autosize()
                    ->helperText(__('Please provide the redemption instructions for the user. for example: provide the coupon code or the gift card number.')),

            ])->action(function (array $data, RewardRedemption $record): void {
                if ($record->user->loyalty_points < $record->reward->required_points) {
                    Notification::make()
                        ->danger()
                        ->title(__('Insufficient Loyalty Points'))
                        ->body(__('The user does not have enough loyalty points to redeem this reward.'))
                        ->send();

                    return;
                }

                DB::transaction(function () use ($data, $record): void {
                    $record->update([
                        'status' => RewardRedemptionStatus::APPROVED,
                        'redemption_instructions' => $data['redemption_instructions'],
                    ]);

                    $record->user->decrement('loyalty_points', $record->reward->required_points);

                    $record->reward->update([
                        'quantity' => $record->reward->quantity - 1,
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('Exchange Request Accepted'))
                        ->body(__('The exchange request has been accepted. The user has been rewarded with the exchange option.'))
                        ->send();
                });

            });
    }
}
