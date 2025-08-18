<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Components\Form;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard\Step;

final class BankInformationStep
{
    public static function make(): Step
    {
        return Step::make('bank_information')
            ->label(__('Bank Credentials'))
            ->icon('phosphor-bank-duotone')
            ->columns(2)
            ->schema([
                TextInput::make('bank_code')
                    ->label(__('Bank Code'))
                    ->required(),

                TextInput::make('bank_account_number')
                    ->label(__('Bank Account Number'))
                    ->required(),

                TextInput::make('iban')
                    ->label(__('IBAN'))
                    ->required(),

                TextInput::make('swift')
                    ->label(__('Swift'))
                    ->required(),
            ]);
    }
}
