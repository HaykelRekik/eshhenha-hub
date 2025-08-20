<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Schemas;

use App\Enums\UserRole;
use App\Filament\Support\Components\AddressBloc;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->hint(__('e,g Riyadh Warehouse'))
                            ->required(),
                        TextInput::make('responsible_name')
                            ->label(__('Responsible Name'))
                            ->required(),
                        Grid::make(3)
                            ->components([
                                PhoneInput::make('responsible_phone_number')
                                    ->label(__('Responsible phone number')),
                                TextInput::make('responsible_email')
                                    ->label(__('Responsible Email'))
                                    ->email(),
                                Select::make('company_id')
                                    ->label(__('Company'))
                                    ->relationship('company', 'name')
                                    ->visible(auth()->user()->hasRole(UserRole::ADMIN))
                                    ->disabled( ! auth()->user()->hasRole(UserRole::ADMIN))
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                            ]),
                        Grid::make(3)
                            ->relationship('address')
                            ->components(AddressBloc::make()),
                    ]),
            ]);
    }
}
