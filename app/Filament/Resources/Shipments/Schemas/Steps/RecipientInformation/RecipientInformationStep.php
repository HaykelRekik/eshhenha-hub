<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas\Steps\RecipientInformation;

use App\Models\Address;
use App\Models\User;
use App\Models\Warehouse;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class RecipientInformationStep
{
    public static function make(): Step
    {
        return Step::make(__('Recipient Information'))
            ->icon(Heroicon::OutlinedMapPin)
            ->schema([
                Grid::make()
                    ->schema([

                        ToggleButtons::make('recipient_mode')
                            ->label(__('Recipient Option'))
                            ->options([
                                'new' => __('Create new recipient'),
                                'existing' => __('Select existing recipient'),
                            ])
                            ->dehydrated(false)
                            ->live()
                            ->partiallyRenderComponentsAfterStateUpdated([
                                'recipient_address_id',
                                'recipient_name',
                                'recipient_phone',
                                'recipient_street',
                                'recipient_city',
                                'recipient_region',
                                'recipient_country',
                                'recipient_zip',
                            ])
                            ->default(fn (): string => 'new')
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                self::resetFields($set);
                            }),

                        Select::make('recipient_address_id')
                            ->label(__('Select Recipient'))
                            ->visibleJs(
                                <<<'JS'
                                            $get('recipient_mode') === 'existing'
                                            JS
                            )
                            ->searchable()
                            ->options(
                                fn (?string $search, Get $get) => Address::query()
                                    ->where('is_recipient_address', true)
                                    ->when(
                                        $get('customer_id'),
                                        fn ($query, $customerId) => $query->where('addressable_type', User::class)
                                            ->where('addressable_id', $customerId)
                                    )

                                    ->when(
                                        $get('company_id'),
                                        function ($query, $companyId) {
                                            $warehousesIds = Warehouse::where('company_id', $companyId)->pluck('id');

                                            return $query->where('addressable_type', Warehouse::class)
                                                ->whereIn('addressable_id', $warehousesIds);
                                        }
                                    )
                                    ->when(
                                        $search,
                                        fn ($q) => $q->where('contact_name', 'like', "%{$search}%")
                                            ->orWhere('contact_phone_number', 'like', "%{$search}%")
                                    )
                                    ->pluck(
                                        DB::raw("CONCAT(contact_name, ' : ', contact_phone_number) as display"),
                                        'id'
                                    )
                            )
                            ->live()
                            ->partiallyRenderComponentsAfterStateUpdated([
                                'recipient_name',
                                'recipient_phone',
                                'recipient_street',
                                'recipient_city',
                                'recipient_region',
                                'recipient_country',
                                'recipient_zip',
                            ])
                            ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                if ($state) {
                                    $address = Address::find($state);
                                    if ($address) {
                                        $set('recipient_name', $address->contact_name);
                                        $set('recipient_phone', $address->contact_phone_number);
                                        $set('recipient_street', $address->street);
                                        $set('recipient_city', $address->city?->{'name_' . app()->getLocale()});
                                        $set('recipient_region', $address->region?->{'name_' . app()->getLocale()});
                                        $set('recipient_country', $address->country?->{'name_' . app()->getLocale()});
                                        $set('recipient_zip', $address->zip_code);

                                    }
                                } else {
                                    $set('recipient_name', null);
                                    $set('recipient_phone', null);
                                    $set('recipient_street', null);
                                    $set('recipient_city', null);
                                    $set('recipient_region', null);
                                    $set('recipient_country', null);
                                    $set('recipient_zip', null);
                                }

                            }),

                        Grid::make()
                            ->visibleJs(
                                <<<'JS'
                                            ($get('recipient_mode') === 'new') || ($get('recipient_mode') === 'existing' && $get('recipient_address_id'))
                                            JS
                            )
                            ->schema([
                                TextInput::make('recipient_name')
                                    ->label(__('Recipient Name'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->required(fn (Get $get): bool => 'new' === $get('recipient_mode'))
                                    ->maxLength(255),

                                PhoneInput::make('recipient_phone')
                                    ->label(__('Recipient Phone'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->required(fn (Get $get): bool => 'new' === $get('recipient_mode')),

                                TextInput::make('recipient_street')
                                    ->label(__('Street Address'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->required(fn (Get $get): bool => 'new' === $get('recipient_mode'))
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('recipient_city')
                                    ->label(__('City'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->required(fn (Get $get): bool => 'new' === $get('recipient_mode'))
                                    ->maxLength(255),

                                TextInput::make('recipient_region')
                                    ->label(__('Region'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->required(fn (Get $get): bool => 'new' === $get('recipient_mode'))
                                    ->maxLength(255),

                                TextInput::make('recipient_country')
                                    ->label(__('Country'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->required(fn (Get $get): bool => 'new' === $get('recipient_mode'))
                                    ->maxLength(255),

                                TextInput::make('recipient_zip')
                                    ->label(__('Zip Code'))
                                    ->disabled(fn (Get $get): bool => 'existing' === $get('recipient_mode'))
                                    ->dehydrated()
                                    ->maxLength(255),
                            ]),
                    ]),

            ]);
    }

    public static function resetFields(Set $set): void
    {
        $set('recipient_address_id', null);
        $set('recipient_name', null);
        $set('recipient_phone', null);
        $set('recipient_street', null);
        $set('recipient_city', null);
        $set('recipient_region', null);
        $set('recipient_country', null);
        $set('recipient_zip', null);
    }
}
