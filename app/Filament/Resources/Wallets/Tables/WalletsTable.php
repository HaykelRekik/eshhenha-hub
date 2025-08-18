<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Tables;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\WalletTransactionType;
use App\Models\Wallet;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
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
                EditAction::make(),
                ViewAction::make(),
                //                ActionGroup::make([
                //
                //                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getAddTransactionAction(): Action
    {
        return Action::make('addTransaction')
            ->label(__('Add Transaction'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->color('primary')
            ->modalHeading(fn (Wallet $record) => __('Add Transaction for :user', ['user' => $record->user->name]))
            ->modalDescription(__('This action will create a new transaction and update the wallet balance accordingly. Please be careful.'))
            ->modalSubmitActionLabel(__('Confirm Transaction'))
            ->schema(static::getTransactionFormSchema())
            ->action(function (array $data, Wallet $record, WalletTransactionService $walletService): void {
                try {
                    $walletService->createTransaction(
                        wallet: $record,
                        type: WalletTransactionType::from($data['type']),
                        amount: (float) $data['amount'],
                        reason: $data['reason']
                    );

                    Notification::make()
                        ->title(__('Transaction created successfully'))
                        ->success()
                        ->send();

                } catch (Exception $e) {
                    Notification::make()
                        ->title(__('Error processing transaction'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected static function getTransactionFormSchema(): array
    {
        return [
            Grid::make()
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->label(__('Transaction Type'))
                        ->options(WalletTransactionType::class)
                        ->required()
                        ->searchable(),

                    TextInput::make('amount')
                        ->label(__('Amount'))
                        ->required()
                        ->numeric()
                        ->minValue(0.01)
                        ->saudiRiyal(),
                ]),

            Textarea::make('reason')
                ->label(__('Reason / Note'))
                ->helperText(__('This will be stored for administrative reference.'))
                ->nullable()
                ->maxLength(500)
                ->rows(3),
        ];
    }
}
