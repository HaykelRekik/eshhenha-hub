<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PricingRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label(__('Company'))
                    ->hidden(fn (Page $livewire): bool => 'customers' === $livewire->activeTab),
                TextColumn::make('shippingCompany.name')
                    ->label(__('Shipping Company')),
                TextColumn::make('weight_from')
                    ->label(__('Weight From'))
                    ->suffix(' ' . __('KG'))
                    ->sortable(),
                TextColumn::make('weight_to')
                    ->label(__('Weight To'))
                    ->suffix(' ' . __('KG')),
                TextColumn::make('local_price_per_kg')
                    ->label(__('Local shipment price'))
                    ->saudiRiyal(),
                TextColumn::make('international_price_per_kg')
                    ->label(__('International shipment price'))
                    ->saudiRiyal(),
            ])
            ->filters([
                SelectFilter::make('Shipping Company')
                    ->label(__('Shipping Company'))
                    ->relationship('shippingCompany', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
