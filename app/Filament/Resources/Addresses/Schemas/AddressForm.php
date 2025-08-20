<?php

declare(strict_types=1);

namespace App\Filament\Resources\Addresses\Schemas;

use App\Filament\Support\Components\AddressBloc;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('label'),
                        TextInput::make('contact_name'),
                        PhoneInput::make('contact_phone_number'),
                        Toggle::make('is_recipient_address')
                            ->required(),
                        Toggle::make('is_default')
                            ->required(),

                        Grid::make()
                            ->components(AddressBloc::make()),

                        TextInput::make('addressable_type')
                            ->required(),
                        TextInput::make('addressable_id')
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }
}
