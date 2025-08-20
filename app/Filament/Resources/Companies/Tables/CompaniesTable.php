<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Company Name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Company Email'))
                    ->searchable(),
                PhoneColumn::make('phone_number')
                    ->label(__('Phone Number'))
                    ->searchable(),
                ImageColumn::make('contracts.logo')
                    ->label(__('Contracts'))
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),

                TextColumn::make('warehouses_count')
                    ->counts('warehouses')
                    ->label(__('Warehouses count'))
                    ->suffix(' ' . __('warehouse')),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
