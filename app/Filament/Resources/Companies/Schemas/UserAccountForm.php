<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class UserAccountForm
{
    public static function make(): array
    {
        return [
            Grid::make()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required(),

                    TextInput::make('email')
                        ->label(__('Email Address'))
                        ->email()
                        ->unique(table: 'users', column: 'email', ignoreRecord: true)
                        ->required(),

                    TextInput::make('password')
                        ->label(__('Password'))
                        ->password()
                        ->revealable()
                        ->required(),

                    PhoneInput::make('phone_number')
                        ->label(__('Phone Number'))
                        ->required()
                        ->unique(table: 'users', column: 'phone_number', ignoreRecord: true),
                ]),
        ];
    }
}
