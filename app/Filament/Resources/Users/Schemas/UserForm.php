<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Rules\SaudiNationalID;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make()
                    ->columnSpanFull()
                    ->columns(3)
                    ->steps([
                        Wizard\Step::make(__('Account Information'))
                            ->description(__('Account general information.'))
                            ->icon('phosphor-identification-card-duotone')
                            ->schema([
                                ToggleButtons::make('role')
                                    ->label(__('Account type'))
                                    ->required()
                                    ->visibleOn('create')
                                    ->inline()
                                    ->live(debounce: 300)
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
                                    ->visibleOn('create')
                                    ->required(),

                                //                            should i install ysfkaya/filament-phone-input ?
                                TextInput::make('phone_number')
                                    ->label(__('Phone Number'))
                                    ->required()
                                    ->tel()
                                    ->unique(table: 'users', column: 'phone_number', ignoreRecord: true),

                                TextInput::make('national_id')
                                    ->label(__('National ID/ Iqama Number'))
                                    ->extraInputAttributes(['maxlength' => 10], true)
                                    ->unique(table: 'users', column: 'national_id', ignoreRecord: true)
                                    ->rule(new SaudiNationalID())
                                    ->hidden(fn (Get $get): bool => $get('role') !== UserRole::USER->value)
                                    ->required(fn (Get $get): bool => $get('role') === UserRole::USER->value),

                                ToggleButtons::make('is_active')
                                    ->label(__('Account Status'))
                                    ->default(true)
                                    ->boolean(
                                        trueLabel: __('Active'),
                                        falseLabel: __('Inactive'),
                                    )
                                    ->required(),
                            ]),
                        Wizard\Step::make(__('Address management'))
                            ->description(__('Default shipping address'))
                            ->icon('phosphor-map-pin-area-duotone')
                            ->hidden(fn (Get $get): bool => $get('role') === UserRole::ADMIN->value)
                            ->schema([
                                Grid::make(3)
                                    ->relationship('address')
                                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => [
                                        ...$data,
                                        'contact_name' => $get('name'),
                                        'contact_phone_number' => $get('phone_number'),
                                        'is_default' => true,
                                    ])
                                    ->schema([
                                        TextInput::make('street')
                                            ->label(__('Street'))
                                            ->columnSpan(2)
                                            ->required(),

                                        TextInput::make('zip_code')
                                            ->label(__('Zip Code'))
                                            ->nullable(),

                                        Select::make('country_id')
                                            ->label(__('Country'))
                                            ->required()
                                            ->options(fn (): Collection => cache()->remember(
                                                key: 'active_countries_' . app()->getLocale(),
                                                ttl: now()->addDay(),
                                                callback: fn () => Country::query()
                                                    ->whereIsActive(true)
                                                    ->orderBy('name_' . app()->getLocale())
                                                    ->pluck('name_' . app()->getLocale(), 'id')
                                            ))
                                            ->searchable()
                                            ->preload()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set): void {
                                                $set('region_id', state: null);
                                                $set('city_id', state: null);
                                            }),

                                        Select::make('region_id')
                                            ->label(__('Region'))
                                            ->required()
                                            ->disabled(fn (Get $get): bool => blank($get('country_id')))
                                            ->searchable()
                                            ->options(function (Get $get): Collection {
                                                $countryId = $get('country_id');

                                                if (blank($countryId)) {
                                                    return collect();
                                                }

                                                return cache()->remember(
                                                    key: "regions_for_country_{$countryId}_" . app()->getLocale(),
                                                    ttl: now()->addHour(),
                                                    callback: fn () => Region::query()
                                                        ->whereIsActive(true)
                                                        ->where('country_id', $countryId)
                                                        ->orderBy('name_' . app()->getLocale())
                                                        ->pluck('name_' . app()->getLocale(), 'id')
                                                );
                                            })
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(
                                                fn (Set $set): mixed => $set('city_id', state: null)
                                            ),

                                        Select::make('city_id')
                                            ->label(__('City'))
                                            ->required()
                                            ->disabled(fn (Get $get): bool => blank($get('region_id')))
                                            ->searchable()
                                            ->options(function (Get $get): Collection {
                                                $regionId = $get('region_id');

                                                if (blank($regionId)) {
                                                    return collect();
                                                }

                                                return cache()->remember(
                                                    key: "cities_for_region_{$regionId}_" . app()->getLocale(),
                                                    ttl: now()->addHour(),
                                                    callback: fn () => City::query()
                                                        ->whereIsActive(true)
                                                        ->where('region_id', $regionId)
                                                        ->pluck('name_' . app()->getLocale(), 'id')
                                                );
                                            }),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
