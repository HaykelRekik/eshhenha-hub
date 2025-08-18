<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

class WelcomeBonusTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Welcome Bonus'))
            ->columns(2)
            ->icon('phosphor-confetti-duotone')
            ->schema([
                TextInput::make('welcome_bonus')
                    ->label(__('Welcome Bonus Points'))
                    ->numeric()
                    ->hintIcon(
                        icon: 'heroicon-o-information-circle',
                        tooltip: __('The points a newly registered user receives upon successful registration as a welcome bonus.')
                    )
                    ->hintColor('info')
                    ->minValue(0)
                    ->default(0)
                    ->suffix(__('point'))
                    ->required(),
            ]);
    }
}
