<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('flag')
                    ->label(__('Country Flag'))
                    ->imageHeight('23px')
                    ->extraAttributes(['loading' => 'lazy']),

                TextColumn::make('name')
                    ->label(__('Country Name'))
                    ->searchable(['name_ar', 'name_en', 'name_ur']),

                TextColumn::make('iso_code')
                    ->label(__('ISO Code'))
                    ->formatStateUsing(fn (string $state): string => mb_strtoupper($state))
                    ->searchable(),

                TextColumn::make('regions_count')
                    ->counts('regions')
                    ->label(__('Regions Count')),

                TextColumn::make('cities_count')
                    ->counts('cities')
                    ->label(__('Cities Count')),

                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),
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
