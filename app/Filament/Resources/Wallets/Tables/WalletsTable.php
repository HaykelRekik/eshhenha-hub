<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Tables;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\Resources\Wallets\Actions\AddTransactionAction;
use App\Models\Wallet;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User account'))
                    ->icon(PhosphorIcons::CardholderDuotone)
                    ->weight(FontWeight::Medium)
                    ->description(fn (Wallet $record): ?string => $record->user?->company?->name)
                    ->searchable(),

                TextColumn::make('balance')
                    ->label(__('Balance'))
                    ->saudiRiyal(),

                TextColumn::make('user.loyalty_points')
                    ->label(__('Loyalty Points'))
                    ->suffix(' ' . __('point')),

                TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->suffix(' ' . __('transaction'))
                    ->label(__('Transactions')),

                IconColumn::make('status')
                    ->label(__('Status'))
                    ->tooltip(fn (Wallet $record): ?string => $record?->lock_reason)
                    ->boolean(),

                TextColumn::make('last_operation_at')
                    ->label(__('Last Transaction Date'))
                    ->placeholder(__('Not specified'))
                    ->dateTime()
                    ->sinceTooltip(),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    AddTransactionAction::make(),

                    ViewAction::make()
                        ->label(__('Wallet details'))
                        ->icon(Heroicon::DocumentMagnifyingGlass),

                    Action::make('lock')
                        ->label(__('Lock Wallet'))
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->hidden(fn ($record) => $record->is_locked)
                        ->requiresConfirmation()
                        ->modalHeading(__('Lock Wallet'))
                        ->modalDescription(__('You are about to lock this wallet. This will prevent the user from making any transactions. Please provide a reason for this action.'))
                        ->schema([
                            Textarea::make('lock_reason')
                                ->label(__('Reason'))
                                ->maxLength(500)
                                ->autosize(),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update([
                                'is_locked' => true,
                                'lock_reason' => $data['lock_reason'],
                            ]);

                            Notification::make()
                                ->success()
                                ->title(__('Wallet Locked Successfully'))
                                ->body(__('The wallet has been locked. The user cannot make transactions until unlocked.'))
                                ->send();
                        }),

                    Action::make('unlock')
                        ->label(__('Activate Wallet'))
                        ->icon(Heroicon::LockOpen)
                        ->color('success')
                        ->hidden(fn ($record): bool => ! $record->is_locked)
                        ->requiresConfirmation()
                        ->modalHeading(__('Unlock Wallet'))
                        ->modalDescription(__('You are about to unlock this wallet. This will allow the user to make transactions again. Please confirm this action.'))
                        ->action(function ($record): void {
                            $record->update([
                                'is_locked' => false,
                                'lock_reason' => null,
                            ]);

                            Notification::make()
                                ->success()
                                ->title(__('Wallet Unlocked Successfully'))
                                ->body(__('The wallet has been unlocked. The user can now make transactions again.'))
                                ->send();

                        }),
                ]),
            ]);
    }
}
