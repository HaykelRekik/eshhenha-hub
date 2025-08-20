<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use App\Enums\Icons\PhosphorIcons;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

final class BalanceToPointsTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Balance To Points Transformation Rules'))
            ->icon(PhosphorIcons::CurrencyEthDuotone)
            ->columns(3)
            ->components([
                TextInput::make('balance_to_points_amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->saudiRiyal()
                    ->helperText(__('Wallet balance amount to convert')),

                TextInput::make('balance_to_points_corresponding_points')
                    ->label(__('Corresponding Points'))
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->step(1)
                    ->helperText(__('How many points this amount equals')),

                TextInput::make('minimum_amount_to_transfer')
                    ->label(__('Minimum Amount to Transfer'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->saudiRiyal()
                    ->helperText(__('Minimum balance amount required to make a transfer')),
            ]);

    }
}
