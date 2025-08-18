<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use App\Enums\Icons\PhosphorIcons;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ContactInformationTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Contact Information'))
            ->icon(PhosphorIcons::ContactlessPaymentDuotone)
            ->columns(2)
            ->schema([
                TextInput::make('contacts.email')
                    ->nullable()
                    ->email()
                    ->label(__('Email Address'))
                    ->prefixIcon(PhosphorIcons::AtDuotone),

                PhoneInput::make('contacts.whatsapp_number')
                    ->nullable()
                    ->onlyCountries(['SA'])
                    ->label(__('Whatsapp Number')),

                PhoneInput::make('contacts.phone_number')
                    ->nullable()
                    ->onlyCountries(['SA'])
                    ->label(__('Phone Number')),

                PhoneInput::make('contacts.second_phone_number')
                    ->nullable()
                    ->onlyCountries(['SA'])
                    ->label(__('Second Phone Number')),
            ]);
    }
}
