<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas\Steps\SenderInformation;

use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Schemas\Steps\RecipientInformation\RecipientInformationStep;
use App\Filament\Resources\Shipments\Schemas\Steps\SenderInformation\SenderInformationSchemas\CompanyInformationSchema;
use App\Filament\Resources\Shipments\Schemas\Steps\SenderInformation\SenderInformationSchemas\CustomerInformationSchema;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Region;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;

class SenderInformationStep
{
    public static function make(): Step
    {
        return Step::make(__('Sender Information'))
            ->icon(Heroicon::OutlinedUser)
            ->schema([
                Grid::make()
                    ->columns(3)
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

                            ->afterStateUpdated(function (Set $set, Get $get, $state): void {
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
                                if ($get('recipient_mode') && 'existing' === $get('recipient_mode')) {
                                    RecipientInformationStep::resetFields($set);
                                }
                            }),

                        // Customer Selection (User with role CUSTOMER)
                        ...CustomerInformationSchema::make(),
                        ...CompanyInformationSchema::make(),
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
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('sender_city_id')
                                    ->label(__('City'))
                                    ->options(fn () => City::get()->pluck('name_' . app()->getLocale(), 'id'))
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('sender_region_id')
                                    ->label(__('Region'))
                                    ->options(fn () => Region::get()->pluck('name_' . app()->getLocale(), 'id'))
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('sender_zip_code')
                                    ->label(__('Zip Code'))
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('sender_country_id')
                                    ->label(__('Country'))
                                    ->options(fn () => Country::get()->pluck('name_' . app()->getLocale(), 'id'))
                                    ->disabled()
                                    ->dehydrated(),

                            ]),
                        Hidden::make('senderable_type')->default(fn (): string => User::class),
                        Hidden::make('senderable_id'),
                    ]),

            ]);
    }
}
