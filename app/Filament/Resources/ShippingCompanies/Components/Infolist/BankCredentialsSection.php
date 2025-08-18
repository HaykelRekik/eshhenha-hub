<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Components\Infolist;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

final class BankCredentialsSection
{
    public static function make(): Section
    {
        return Section::make(__('Bank Credentials'))
            ->icon('phosphor-bank-duotone')
            ->columns(2)
            ->collapsible()
            ->schema([
                TextEntry::make('bank_code')
                    ->label(__('Bank Code')),
                TextEntry::make('bank_account_number')
                    ->label(__('Bank Account Number')),
                TextEntry::make('iban')
                    ->label(__('IBAN')),
                TextEntry::make('swift')
                    ->label(__('Swift')),
            ]);
    }
}
