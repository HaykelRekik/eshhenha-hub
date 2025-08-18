<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use App\Enums\Icons\PhosphorIcons;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class WelcomeBonusTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Welcome Bonus'))
            ->columns(2)
            ->icon(PhosphorIcons::ConfettiDuotone)
            ->components([
                TextInput::make('welcome_bonus')
                    ->label(__('Welcome Bonus Points'))
                    ->numeric()
                    ->hintIcon(
                        icon: Heroicon::OutlinedInformationCircle,
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
