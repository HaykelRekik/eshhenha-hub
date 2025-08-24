<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Actions;

use App\Enums\RewardRedemptionStatus;
use App\Models\RewardRedemption;
use Filament\Actions\Action;

class RejectExchangeRequestAction
{
    public static function make(): Action
    {
        return Action::make('rejectExchangeRequest')
            ->label(__('Reject'))
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-x-mark')
            ->requiresConfirmation()
            ->visible(fn (RewardRedemption $record): bool => RewardRedemptionStatus::NEW === $record->status)
            ->action(function ($record): void {
                $record->update([
                    'status' => RewardRedemptionStatus::REJECTED,
                ]);
            });
    }
}
