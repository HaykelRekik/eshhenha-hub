<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Schemas;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Select::make('user_id')
                            ->label(__('User account'))
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->role(UserRole::COMPANY)->whereDoesntHave('company')
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabledOn('edit')
                            ->createOptionForm([
                                Grid::make(2)
                                    ->schema([
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
                                            ->required(),

                                        TextInput::make('phone_number')
                                            ->label(__('Phone Number'))
                                            ->required()
                                            ->tel()
                                            ->unique(table: 'users', column: 'phone_number', ignoreRecord: true),
                                    ]),
                            ])
                            ->createOptionUsing(fn (array $data): int => User::create([
                                ...$data,
                                'role' => UserRole::COMPANY,
                            ])->getKey()),

                        TextInput::make('name')
                            ->label(__('Company Name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(__('Contact Email'))
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone_number')
                            ->tel()
                            ->label(__('Contact Phone Number'))
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('shipping_companies')
                            ->label(__('Contracts'))
                            ->relationship(
                                name: 'contracts',
                                titleAttribute: 'name'
                            )->searchable()
                            ->multiple()
                            ->hint(__('Shipping Companies'))
                            ->preload(),

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

                        FileUpload::make('logo')
                            ->label(__('Company Logo'))
                            ->columnSpanFull()
                            ->nullable()
                            ->image()
                            ->directory('companies')
                            ->maxSize(1024),

                    ]),
            ]);
    }
}
