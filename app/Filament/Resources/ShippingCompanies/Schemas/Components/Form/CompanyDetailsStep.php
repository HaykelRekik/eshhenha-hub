<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Schemas\Components\Form;

use App\Filament\Support\Components\AddressBloc;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class CompanyDetailsStep
{
    public static function make(): Step
    {
        return Step::make('company_details')
            ->label(__('Company Details'))
            ->icon('phosphor-identification-card-duotone')
            ->columns(4)
            ->schema([
                TextInput::make('name')
                    ->label(__('Company Name'))
                    ->required(),

                TextInput::make('email')
                    ->label(__('Email Address'))
                    ->email()
                    ->unique(table: 'shipping_companies', column: 'email', ignoreRecord: true)
                    ->required(),

                PhoneInput::make('phone_number')
                    ->label(__('Phone Number'))
                    ->unique(table: 'shipping_companies', column: 'phone_number', ignoreRecord: true)
                    ->required(),

                ToggleButtons::make('is_active')
                    ->label(__('Status'))
                    ->default(true)
                    ->boolean(
                        trueLabel: __('Active'),
                        falseLabel: __('Inactive'),
                    )
                    ->required(),

                Grid::make(3)
                    ->relationship('address')
                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => [
                        ...$data,
                        'contact_name' => $get('name'),
                        'contact_phone_number' => $get('phone_number'),
                        'is_default' => true,
                    ])
                    ->components([
                        AddressBloc::make(),
                    ]),

                FileUpload::make('logo')
                    ->label(__('Company Logo'))
                    ->columnSpanFull()
                    ->required()
                    ->image()
                    ->directory('shipping-companies')
                    ->maxSize(1024),
            ]);
    }
}
