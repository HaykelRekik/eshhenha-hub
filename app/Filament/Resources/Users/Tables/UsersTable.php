<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('User name'))
                    ->searchable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('email')
                    ->label(__('Contact Information'))
                    ->description(fn ($record): ?string => $record->phone_number)
                    ->searchable(['email', 'phone_number']),
                TextColumn::make('loyalty_points')
                    ->default(0)
                    ->label(__('loyalty_points'))
                    ->suffix(' ' . __('point')),
                TextColumn::make('user.wallet.balance')
                    ->label(__('Wallet Balance'))
                    ->default(0)
                    ->saudiRiyal(),
                TextColumn::make('role')
                    ->label(__('Account Type'))
                    ->badge()
                    ->hidden(fn (Page $livewire): bool => 'all' !== $livewire->activeTab),
                IconColumn::make('is_active')
                    ->label(__('Account Status'))
                    ->boolean(),
                TextColumn::make('last_login_at')
                    ->label(__('Last Login'))
                    ->placeholder(__('Not specified'))
                    ->dateTime()
                    ->sinceTooltip(),
                TextColumn::make('created_at')
                    ->label(__('Registration Date'))
                    ->dateTime()
                    ->sinceTooltip(),
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
