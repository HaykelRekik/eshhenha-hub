<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Schemas\Components\Form;

use App\Filament\Support\Components\BankDetailsBloc;
use Filament\Schemas\Components\Wizard\Step;

final class BankInformationStep
{
    public static function make(): Step
    {
        return Step::make('bank_information')
            ->label(__('Bank Credentials'))
            ->icon('phosphor-bank-duotone')
            ->columns(2)
            ->schema(BankDetailsBloc::make());
    }
}
