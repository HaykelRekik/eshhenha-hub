<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Models\Wallet;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WalletInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading(__('Wallet Information'))
                    ->icon(PhosphorIcons::WalletDuotone)
                    ->components([
                        Grid::make()
                            ->columns(6)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('User account'))
                                    ->icon(Heroicon::OutlinedUser)
                                    ->tooltip(fn (Wallet $record): string => $record->user->role->getLabel() . ' ' . $record->user?->company?->name),

                                TextEntry::make('balance')
                                    ->label(__('Balance'))
                                    ->icon(PhosphorIcons::Coins)
                                    ->saudiRiyal(),

                                TextEntry::make('user.loyalty_points')
                                    ->label(__('Loyalty Points'))
                                    ->icon(PhosphorIcons::Medal)
                                    ->suffix(' ' . __('point')),

                                TextEntry::make('transactions_count')
                                    ->label(__('Transactions Count'))
                                    ->getStateUsing(fn (Wallet $record): string => (string) $record->transactions()->count())
                                    ->icon(PhosphorIcons::CardsThree)
                                    ->suffix(' ' . __('transaction')),

                                TextEntry::make('last_operation_at')
                                    ->label(__('Last Transaction Date'))
                                    ->dateTime('d M Y , h:i A')
                                    ->placeholder(__('Not specified'))
                                    ->sinceTooltip(),

                                IconEntry::make('status')
                                    ->label(__('Wallet status'))
                                    ->boolean()
                                    ->tooltip(fn (Wallet $record): ?string => $record?->lock_reason),

                            ]),
                    ]),
            ]);
    }
}
