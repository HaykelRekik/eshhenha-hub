<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RewardRedemptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reward.name')
                    ->label(__('Product Name'))
                    ->description(fn ($record): ?string => '#' . $record->reference)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->description(fn ($record): ?string => $record->user?->role->getLabel())
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Request Status'))
                    ->badge(),

                TextColumn::make('created_at')
                    ->label(__('Request date'))
                    ->dateTime()
                    ->sortable(),

            ])
            ->filters([

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
