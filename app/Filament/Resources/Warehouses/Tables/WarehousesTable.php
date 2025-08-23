<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Tables;

use App\Models\Warehouse;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class WarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Warehouse'))
                    ->description(fn (Warehouse $record): string => $record->company->name)
                    ->searchable(),

                TextColumn::make('responsible_name')
                    ->label(__('Responsible'))
                    ->description(fn (Warehouse $record): ?string => $record->responsible_email)
                    ->searchable(),

                PhoneColumn::make('responsible_phone_number')
                    ->label(__('Responsible phone number'))
                    ->searchable(),

                TextColumn::make('address.full_address')
                    ->label(__('Address'))
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('Company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
