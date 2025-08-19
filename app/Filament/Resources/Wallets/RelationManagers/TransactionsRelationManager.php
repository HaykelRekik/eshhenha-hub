<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\RelationManagers;

use App\Enums\WalletTransactionType;
use App\Models\Wallet;
use App\Services\Wallets\WalletTransactionService;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static bool $isLazy = false;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Transactions');
    }

    public static function getModelLabel(): ?string
    {
        return __('transaction');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('identifier')
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

                TextColumn::make('performedBy.name')
                    ->label(__('Performed By')),

                TextColumn::make('metadata')
                    ->label(__('Transaction Note'))
                    ->placeholder('--'),

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
            ->headerActions([

                Action::make('create-transaction')
                    ->label(fn (): string => __('filament-actions::create.single.label', ['label' => static::getModelLabel()]))
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->modalHeading(fn (): string => __('filament-actions::create.single.modal.heading', ['label' => static::getModelLabel()]))
                    ->modalSubmitActionLabel(__('Confirm Transaction'))
                    ->schema([
                        Section::make()
                            ->schema([
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
                            ]),
                    ])
                    ->action(function (array $data, WalletTransactionService $walletTransactionService): void {
                        $wallet = Wallet::find($this->ownerRecord->id);
                        try {
                            $transaction = $walletTransactionService->createTransaction(
                                wallet: $wallet,
                                type: WalletTransactionType::from($data['type']->value),
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
                    }),
            ])
            ->recordActions([
                //                EditAction::make(),
                //                DeleteAction::make(),
            ]);
    }
}
