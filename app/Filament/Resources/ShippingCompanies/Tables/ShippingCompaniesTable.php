<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Tables;

use App\Models\ShippingCompany;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class ShippingCompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Company'))
                    ->description(fn (ShippingCompany $record): string => $record->email)
                    ->weight(FontWeight::SemiBold)
                    ->searchable(),

                PhoneColumn::make('phone_number')
                    ->label(__('Phone Number'))
                    ->searchable(),

                TextColumn::make('address.full_address')
                    ->words(5)
                    ->label(__('Address')),

                TextColumn::make('shipping_range')
                    ->label(__('Shipping Range'))
                    ->badge(),

                TextColumn::make('shipments_count')
                    ->label(__('Shipments Count'))
                    ->default(0)
                    ->suffix(' ' . __('shipment')),

                IconColumn::make('has_home_pickup')
                    ->label(__('Home Pickup'))
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),
            ])
            ->filters([

            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([

            ]);
    }
}
