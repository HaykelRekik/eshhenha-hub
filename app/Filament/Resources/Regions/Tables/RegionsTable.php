<?php

declare(strict_types=1);

namespace App\Filament\Resources\Regions\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')
                    ->label(__('Country Name')),

                TextColumn::make('name_ar')
                    ->label(__('Name (Arabic)'))
                    ->searchable(),
                TextColumn::make('name_en')
                    ->label(__('Name (English)'))
                    ->searchable(),
                TextColumn::make('name_ur')
                    ->label(__('Name (Urdu)'))
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('Country Name'))
                    ->relationship('country', 'name_' . app()->getLocale())
                    ->preload()
                    ->searchable(['name_ar', 'name_en', 'name_ur']),
            ])
            ->deferFilters()
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
