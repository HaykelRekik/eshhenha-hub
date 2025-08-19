<?php

declare(strict_types=1);

namespace App\Filament\Resources\WalletTransactions\Tables;

use App\Enums\WalletTransactionType;
use App\Models\WalletTransaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WalletTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('Transaction Type'))
                    ->badge(),

                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->saudiRiyal(),

                TextColumn::make('balance_after')
                    ->label(__('Balance After Operation'))
                    ->saudiRiyal(),

                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->description(fn (WalletTransaction $record): string => $record->user?->role->getLabel() . ' ' . $record->user?->company?->name),

                TextColumn::make('performedBy.name')
                    ->label(__('Performed By')),

                TextColumn::make('created_at')
                    ->label(__('Operation date'))
                    ->dateTime()
                    ->sinceTooltip(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Transaction Type'))
                    ->options(WalletTransactionType::class)
                    ->searchable(),
            ])
            ->recordActions([
            ]);
    }
}
