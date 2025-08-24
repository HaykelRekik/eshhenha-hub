<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\Resources\RewardRedemptions\RewardRedemptionResource;
use App\Filament\Resources\Rewards\RewardResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListRewards extends ListRecords
{
    protected static string $resource = RewardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('view_exchange_requests')
                ->label(__('View exchange requests'))
                ->outlined()
                ->color(Color::Amber)
                ->icon(PhosphorIcons::ArrowsCounterClockwiseDuotone)
                ->url(RewardRedemptionResource::getUrl('index')),
        ];
    }
}
