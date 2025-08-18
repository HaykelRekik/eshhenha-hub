<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Components\Infolist;

use App\Enums\ShippingCompanyInsuranceType;
use App\Models\ShippingCompany;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

final class ShippingAndInsuranceSection
{
    public static function make(): Section
    {

        return Section::make(__('Shipping And Insurance Settings'))
            ->icon('phosphor-package-duotone')
            ->columns(1)
            ->collapsible()
            ->schema([
                Grid::make()
                    ->columns(6)
                    ->schema([
                        TextEntry::make('shipping_range')
                            ->label(__('Shipping Range'))
                            ->badge(),

                        TextEntry::make('shipments_count')
                            ->label(__('Shipments Count'))
                            ->suffix(' ' . __('shipment'))
                            ->default(0),

                        TextEntry::make('insurance_type')
                            ->label(__('Insurance Type')),

                        /** Workaround to dynamically display the suffix */
                        TextEntry::make('insurance_value')
                            ->label(__('Insurance Value'))
                            ->saudiRiyal()
                            ->visible(fn (ShippingCompany $record): bool => ShippingCompanyInsuranceType::AMOUNT === $record->insurance_type),

                        TextEntry::make('insurance_value')
                            ->label(__('Insurance Value'))
                            ->suffix('%')
                            ->visible(fn (ShippingCompany $record): bool => ShippingCompanyInsuranceType::PERCENTAGE === $record->insurance_type),
                        /** End Workaround to dynamically display the suffix */
                        IconEntry::make('has_home_pickup')
                            ->label(__('Home Pickup ?'))
                            ->boolean(),

                        TextEntry::make('home_pickup_cost')
                            ->label(__('Home Pickup Cost'))
                            ->saudiRiyal()
                            ->visible(fn (ShippingCompany $record): bool => $record->has_home_pickup),

                        TextEntry::make('contracts.name')
                            ->label(__('Contracted companies'))
                            ->badge()
                            ->color('info')
                            ->placeholder(__('No contracts found.'))
                            ->columnSpanFull(),
                    ])->columnSpan(1),

                Grid::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('deliveryZones.name_' . app()->getLocale())
                            ->badge()
                            ->label(__('Delivery Zones'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
