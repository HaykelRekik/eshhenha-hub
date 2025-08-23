<?php

declare(strict_types=1);

namespace App\Filament\Support\Components;

use App\Rules\SaudiIBAN;
use App\Rules\SwiftCode;
use Filament\Forms\Components\TextInput;

final class BankDetailsBloc
{
    public static function make(): array
    {
        return
            [
                TextInput::make('bank_code')
                    ->label(__('Bank Code'))
                    ->required(),

                TextInput::make('bank_account_number')
                    ->label(__('Bank Account Number'))
                    ->unique()
                    ->required(),

                TextInput::make('iban')
                    ->label(__('IBAN'))
                    ->required()
                    ->unique()
                    ->rules([new SaudiIBAN()]),

                TextInput::make('swift')
                    ->label(__('Swift'))
                    ->required()
                    ->rules([new SwiftCode()]),
            ];
    }
}
