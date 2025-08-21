<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Tables;

use App\Enums\PricingRuleType;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PricingRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('Rule Type'))
                    ->badge(),

                TextColumn::make('company.name')
                    ->label(__('Company'))
                    ->visible(fn (HasTable $livewire): bool => 'company' === ($livewire->getTableFilterState('type')['value'] ?? null)),
                TextColumn::make('shippingCompany.name')
                    ->label(__('Shipping Company'))
                    ->visible(fn (HasTable $livewire): bool => 'shipping company' === ($livewire?->getTableFilterState('type')['value'] ?? null)),

                TextColumn::make('user.name')
                    ->label(__('Customer'))
                    ->visible(fn (HasTable $livewire): bool => 'customer' === ($livewire?->getTableFilterState('type')['value'] ?? null)),

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
                SelectFilter::make('type')
                    ->label(__('Pricing rule for'))
                    ->options(PricingRuleType::class),

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

            ]);
    }
}
