<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Components\Form;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Collection;
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
                                $set(key: 'region_id', state: null);
                                $set(key: 'city_id', state: null);
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
                                fn (Set $set): mixed => $set(key: 'city_id', state: null)
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
