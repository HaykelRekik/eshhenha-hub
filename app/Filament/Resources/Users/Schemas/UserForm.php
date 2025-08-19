<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\UserRole;
use App\Filament\Support\Components\AddressBloc;
use App\Rules\SaudiNationalID;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make()
                    ->columns(3)
                    ->steps([
                        Step::make(__('Account Information'))
                            ->icon(PhosphorIcons::IdentificationCardDuotone)
                            ->schema([
                                ToggleButtons::make('role')
                                    ->label(__('Account type'))
                                    ->required()
                                    ->visibleOn('create')
                                    ->inline()
                                    ->options(UserRole::class),

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
                                    ->visibleOn(Operation::Create)
                                    ->required(),

                                PhoneInput::make('phone_number')
                                    ->label(__('Phone Number'))
                                    ->required()
                                    ->unique(table: 'users', column: 'phone_number', ignoreRecord: true),

                                TextInput::make('national_id')
                                    ->label(__('National ID/ Iqama Number'))
                                    ->extraInputAttributes(['maxlength' => 10], true)
                                    ->unique(table: 'users', column: 'national_id', ignoreRecord: true)
                                    ->rule(new SaudiNationalID())
                                    ->hiddenJs(
                                        <<<'JS'
                                            $get('role') !== 'customer'
                                          JS
                                    )
                                    ->requiredIf('role', UserRole::USER->value)
                                    ->validationMessages([
                                        'required_if' => __('This field is required when registering a new customer.'),
                                    ]),

                                Fieldset::make()
                                    ->label(__('Default shipping address'))
                                    ->visibleJs(
                                        <<<'JS'
                                            $get('role') === 'customer'
                                          JS
                                    )
                                    ->dehydratedWhenHidden(false)
                                    ->components([
                                        Grid::make(3)
                                            ->relationship('address')
                                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => [
                                                ...$data,
                                                'contact_name' => $get('name'),
                                                'contact_phone_number' => $get('phone_number'),
                                                'is_default' => true,
                                            ])
                                            ->components(AddressBloc::make()),
                                    ]),

                                Fieldset::make()
                                    ->label(__('Company Informations'))
                                    ->visibleJs(
                                        <<<'JS'
                                            $get('role') === 'company'
                                          JS
                                    )
                                    ->components([
                                        Grid::make(3)
                                            ->relationship('company')
                                            ->components(
                                                [
                                                    TextInput::make('company_name')
                                                        ->label(__('Company Name')),
                                                ]
                                            )
                                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => [
                                                ...$data,
                                                'name' => $get('company_name'),
                                                'email',
                                                'phone_number' => $get('phone_number'),
                                                'logo',
                                                'is_active' => true,
                                            ]),
                                    ]),

                                ToggleButtons::make('is_active')
                                    ->label(__('Account Status'))
                                    ->default(true)
                                    ->boolean(
                                        trueLabel: __('Active'),
                                        falseLabel: __('Inactive'),
                                    )
                                    ->required(),
                            ]),

                    ]),
            ]);
    }
}
