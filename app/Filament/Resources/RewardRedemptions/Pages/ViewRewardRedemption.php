<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Pages;

use App\Filament\Resources\RewardRedemptions\Actions\AcceptExchangeRequestAction;
use App\Filament\Resources\RewardRedemptions\Actions\RejectExchangeRequestAction;
use App\Filament\Resources\RewardRedemptions\RewardRedemptionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRewardRedemption extends ViewRecord
{
    protected static string $resource = RewardRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AcceptExchangeRequestAction::make(),
            RejectExchangeRequestAction::make(),
        ];
    }
}
