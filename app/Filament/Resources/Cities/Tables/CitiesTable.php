<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->searchable()
                    ->label(__('Name (Arabic)')),

                TextColumn::make('name_en')
                    ->searchable()
                    ->label(__('Name (English)')),

                TextColumn::make('name_ur')
                    ->searchable()
                    ->label(__('Name (Urdu)')),

                TextColumn::make('region.name_' . app()->getLocale())
                    ->label(__('Region')),

                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),

            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label(__('Region'))
                    ->relationship('region', 'name_' . app()->getLocale())
                    ->preload()
                    ->searchable(['name_ar', 'name_en', 'name_ur']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
