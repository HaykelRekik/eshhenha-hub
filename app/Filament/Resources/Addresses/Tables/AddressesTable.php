<?php

declare(strict_types=1);

namespace App\Filament\Resources\Addresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->badge()
                    ->color(Color::Neutral)
                    ->placeholder(__('Not specified')),
                TextColumn::make('contact_name')
                    ->placeholder(__('Not specified'))
                    ->searchable(),
                TextColumn::make('contact_phone_number')
                    ->searchable(),
                TextColumn::make('full_address')
                    ->label(__('Full Address')),

                IconColumn::make('is_default')
                    ->boolean(),
                IconColumn::make('is_recipient_address')
                    ->boolean(),

                TextColumn::make('addressable.name')
                    ->searchable(),

            ])
            ->filters([

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
