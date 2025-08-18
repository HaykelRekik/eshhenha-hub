<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use App\Enums\Icons\PhosphorIcons;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

final class ReferralSystemTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Referral System'))
            ->icon(PhosphorIcons::GiftDuotone)
            ->columns(2)
            ->components([
                TextInput::make('referrer_user_points')
                    ->label(__('Referrer User Points'))
                    ->numeric()
                    ->hintIcon(
                        icon: Heroicon::OutlinedInformationCircle,
                        tooltip: __('the number of points the referrer earns when someone they invited signs up successfully. These points are automatically credited to the referrer\'s account upon the referred user\'s registration.')
                    )
                    ->hintColor('info')
                    ->minValue(0)
                    ->default(0)
                    ->suffix(__('point'))
                    ->required(),

                TextInput::make('referred_user_points')
                    ->label(__('Referred User Points'))
                    ->numeric()
                    ->hintIcon(
                        icon: Heroicon::OutlinedInformationCircle,
                        tooltip: __('the number of points a newly registered user receives when they sign up using a referral code. These points are automatically credited to their account upon successful registration.')
                    )
                    ->hintColor('info')
                    ->minValue(0)
                    ->default(0)
                    ->suffix(__('point'))
                    ->required(),
            ]);
    }
}
