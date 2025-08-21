<?php

declare(strict_types=1);

namespace App\Filament\Support\Components;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Collection;

final class AddressBloc
{
    public static function make(): array
    {
        return [
            Grid::make()
                ->columns(4)
                ->components([
                    TextInput::make('street')
                        ->label(__('Street'))
                        ->columnSpan(3)
                        ->required(),

                    TextInput::make('zip_code')
                        ->label(__('Zip Code'))
                        ->columnSpan(1)
                        ->nullable(),
                ]),

            Grid::make()
                ->columns(3)
                ->components([
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
                        ->partiallyRenderComponentsAfterStateUpdated(['region_id', 'city_id'])
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
                        ->partiallyRenderComponentsAfterStateUpdated(['city_id'])
                        ->afterStateUpdated(function (Set $set): void {
                            $set(key: 'city_id', state: null);
                        }),

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
        ];
    }
}
