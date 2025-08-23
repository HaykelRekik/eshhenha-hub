<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Region;
use App\Models\User;
use App\Models\Warehouse;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // Step 1: Sender Information
                    Step::make(__('Sender Information'))
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    ToggleButtons::make('sender_type')
                                        ->label(__('Sender Type'))
                                        ->options([
                                            'customer' => __('Customer'),
                                            'company' => __('Company'),
                                        ])
                                        ->visible(fn (): bool => UserRole::ADMIN === auth()->user()->role)
                                        ->required()
                                        ->dehydrated(false)
                                        ->live()
                                        ->partiallyRenderComponentsAfterStateUpdated([
                                            'customer_id', 'address_id', 'company_id', 'warehouse_id', 'senderable_type', 'senderable_id', 'sender_street', 'sender_city_id', 'sender_region_id', 'sender_country_id', 'sender_zip_code',
                                        ])
                                        ->default(fn (): string => UserRole::COMPANY === auth()->user()->role ? 'company' : 'customer')

                                        ->afterStateUpdated(function (Set $set, $state): void {
                                            if ('company' === $state) {
                                                $set('senderable_type', Company::class);
                                            }
                                            if ('customer' === $state) {
                                                $set('senderable_type', User::class);
                                            }
                                            $set('customer_id', null);
                                            $set('address_id', null);
                                            $set('company_id', null);
                                            $set('warehouse_id', null);
                                            $set('senderable_id', null);
                                            $set('sender_street', null);
                                            $set('sender_city_id', null);
                                            $set('sender_region_id', null);
                                            $set('sender_country_id', null);
                                            $set('sender_zip_code', null);
                                        }),

                                    // Customer Selection (User with role CUSTOMER)
                                    Select::make('customer_id')
                                        ->label(__('Customer'))
                                        ->options(function () {
                                            if (UserRole::USER === auth()->user()->role) {
                                                return User::where('id', auth()->user()->id)->get()->pluck('name', 'id');
                                            }

                                            return User::where('role', UserRole::USER)
                                                ->where('is_active', true)
                                                ->get()
                                                ->pluck('name', 'id');

                                        })
                                        ->default(fn () => UserRole::USER === auth()->user()->role ? auth()->user()->id : null)
                                        ->disabled(fn (): bool => UserRole::USER === auth()->user()->role)
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->partiallyRenderComponentsAfterStateUpdated([
                                            'address_id',
                                            'sender_street',
                                            'sender_city_id',
                                            'sender_region_id',
                                            'sender_country_id',
                                            'sender_zip_code',
                                        ])
                                        ->visibleJs(
                                            <<<'JS'
                                            $get('sender_type') === 'customer'
                                            JS
                                        )
                                        ->required(fn (Get $get): bool => 'customer' === $get('sender_type'))
                                        ->afterStateUpdated(function (Set $set, $state): void {
                                            $set('senderable_id', (int) ($state));
                                            $set('address_id', null);
                                            $set('sender_street', null);
                                            $set('sender_city_id', null);
                                            $set('sender_region_id', null);
                                            $set('sender_country_id', null);
                                            $set('sender_zip_code', null);
                                        }),

                                    // Customer address selection
                                    Select::make('address_id')
                                        ->label(__('Address'))
                                        ->options(function (Get $get) {
                                            $customerId = $get('customer_id');
                                            if ( ! $customerId) {
                                                return collect();
                                            }

                                            return Address::where('addressable_type', User::class)
                                                ->where('addressable_id', $customerId)
                                                ->get()
                                                ->pluck('full_address', 'id');

                                        })
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->partiallyRenderComponentsAfterStateUpdated([
                                            'sender_street',
                                            'sender_city_id',
                                            'sender_region_id',
                                            'sender_country_id',
                                            'sender_zip_code',
                                        ])
                                        ->visibleJs(
                                            <<<'JS'
                                            $get('sender_type') === 'customer' && $get('customer_id')
                                            JS
                                        )
                                        ->required(fn (Get $get): bool => 'customer' === $get('sender_type'))
                                        ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                            if ('customer' === $get('sender_type')) {
                                                $customerId = $get('customer_id');
                                                if ($customerId && $state) {
                                                    $user = User::find($customerId);
                                                    $address = Address::find($state);
                                                    if ($user && $address) {
                                                        $set('sender_street', $address->street);
                                                        $set('sender_zip_code', $address->zip_code);
                                                        $set('sender_city_id', $address->city?->id);
                                                        $set('sender_region_id', $address->region?->id);
                                                        $set('sender_country_id', $address->country?->id);

                                                    }
                                                }
                                            }
                                        }),
                                    // Company Selection
                                    Select::make('company_id')
                                        ->label(__('Company'))
                                        ->options(function () {
                                            if (UserRole::COMPANY === auth()->user()->role) {
                                                return Company::where('user_id', auth()->user()->id)->get()->pluck('name', 'id');
                                            }

                                            return Company::where('is_active', true)
                                                ->get()
                                                ->pluck('name', 'id');

                                        })
                                        ->default(fn () => UserRole::COMPANY === auth()->user()->role ? auth()->user()->company()->sole()->id : null)
                                        ->disabled(fn (): bool => UserRole::COMPANY === auth()->user()->role)
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->partiallyRenderComponentsAfterStateUpdated([
                                            'warehouse_id',
                                            'sender_street',
                                            'sender_city_id',
                                            'sender_region_id',
                                            'sender_country_id',
                                            'sender_zip_code',
                                        ])
                                        ->visibleJs(
                                            <<<'JS'
                                            $get('sender_type') === 'company'
                                            JS
                                        )
                                        ->required(fn (Get $get): bool => 'company' === $get('sender_type'))
                                        ->afterStateUpdated(function (Set $set, $state): void {
                                            $set('senderable_id', (int) ($state));
                                            $set('warehouse_id', null);
                                            $set('sender_street', null);
                                            $set('sender_city_id', null);
                                            $set('sender_region_id', null);
                                            $set('sender_country_id', null);
                                            $set('sender_zip_code', null);

                                        }),

                                    // Select Company's Warehouse
                                    Select::make('warehouse_id')
                                        ->label(__('Warehouse'))
                                        ->options(function (Get $get) {
                                            $companyId = $get('company_id');
                                            if ( ! $companyId) {
                                                return collect();
                                            }

                                            return Warehouse::where('company_id', $companyId)
                                                ->get()
                                                ->pluck('name', 'id');
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->partiallyRenderComponentsAfterStateUpdated([
                                            'sender_street',
                                            'sender_city_id',
                                            'sender_region_id',
                                            'sender_country_id',
                                            'sender_zip_code',
                                        ])
                                        ->visibleJs(
                                            <<<'JS'
                                            $get('sender_type') === 'company' && $get('company_id')
                                            JS
                                        )
                                        ->required(fn (Get $get): bool => 'company' === $get('sender_type'))
                                        ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                            if ('company' === $get('sender_type')) {
                                                $companyId = $get('company_id');
                                                if ($companyId && $state) {
                                                    $company = Company::find($companyId);
                                                    $warehouse = Warehouse::find($state);
                                                    if ($company && $warehouse) {
                                                        $warehouseAddress = $warehouse->addresses()->first();
                                                        $set('sender_street', $warehouseAddress->street);
                                                        $set('sender_city_id', $warehouseAddress->city?->id);
                                                        $set('sender_region_id', $warehouseAddress->region?->id);
                                                        $set('sender_country_id', $warehouseAddress->country?->id);
                                                        $set('sender_zip_code', $warehouseAddress->zip_code);
                                                    }
                                                }
                                            }
                                        }),

                                    Section::make()
                                        ->label(__('Full Address'))
                                        ->visibleJs(
                                            <<<'JS'
                                            ('company' === $get('sender_type') && $get('warehouse_id')) ||('customer' === $get('sender_type') && $get('address_id'))
                                            JS
                                        )
                                        ->schema([
                                            TextInput::make('sender_street')
                                                ->label(__('Street'))
                                                ->disabled(),

                                            Select::make('sender_city_id')
                                                ->label(__('City'))
                                                ->options(fn () => City::get()->pluck('name_' . app()->getLocale(), 'id'))
                                                ->disabled(),

                                            Select::make('sender_region_id')
                                                ->label(__('Region'))
                                                ->options(fn () => Region::get()->pluck('name_' . app()->getLocale(), 'id'))
                                                ->disabled(),

                                            TextInput::make('sender_zip_code')
                                                ->label(__('Zip Code'))
                                                ->disabled(),

                                            Select::make('sender_country_id')
                                                ->label(__('Country'))
                                                ->options(fn () => Country::get()->pluck('name_' . app()->getLocale(), 'id'))
                                                ->disabled(),

                                        ]),
                                    Hidden::make('senderable_type')->default(fn (): string => User::class),
                                    Hidden::make('senderable_id'),
                                    Hidden::make('sender_street'),
                                    Hidden::make('sender_city_id'),
                                    Hidden::make('sender_region_id'),
                                    Hidden::make('sender_zip_code'),
                                    Hidden::make('sender_country_id'),

                                ]),

                        ]),

                ]),
            ]);
    }
}
