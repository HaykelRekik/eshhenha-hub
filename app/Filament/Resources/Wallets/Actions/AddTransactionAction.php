<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Actions;

use App\Enums\WalletTransactionType;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletTransactionService;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;

class AddTransactionAction
{
    public static function make(): Action
    {
        return Action::make('addTransaction')
            ->label(__('Add Transaction'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->color('primary')
            ->modalHeading(fn (Wallet $record) => __('Add Transaction for :user', ['user' => $record->user->name]))
            ->modalDescription(__('This action will create a new transaction and update the wallet balance accordingly. Please be careful.'))
            ->modalSubmitActionLabel(__('Confirm Transaction'))
            ->schema(static::getTransactionFormSchema())
            ->action(function (array $data, Wallet $record, WalletTransactionService $walletService): WalletTransaction {
                try {
                    $walletTransaction = $walletService->createTransaction(
                        wallet: $record,
                        type: WalletTransactionType::from($data['type']->value),
                        amount: (float) $data['amount'],
                        reason: $data['reason']
                    );

                    Notification::make()
                        ->title(__('Transaction created successfully'))
                        ->success()
                        ->send();

                    return $walletTransaction;
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
