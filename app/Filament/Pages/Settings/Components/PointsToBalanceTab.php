<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use App\Enums\Icons\PhosphorIcons;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

final class PointsToBalanceTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Points To Balance Transformation Rules'))
            ->icon(PhosphorIcons::CoinsDuotone)
            ->columns(3)
            ->components([
                TextInput::make('points_to_balance_points')
                    ->label(__('Number of Points'))
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->step(1)
                    ->helperText(__('How many points will be converted')),

                TextInput::make('points_to_balance_amount')
                    ->label(__('Corresponding Amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->saudiRiyal()
                    ->helperText(__('The wallet balance amount these points equal')),

                TextInput::make('minimum_transferable_points')
                    ->label(__('Minimum Transferable Points'))
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->step(1)
                    ->helperText(__('Minimum points required to make a transfer')),
            ]);

    }
}
