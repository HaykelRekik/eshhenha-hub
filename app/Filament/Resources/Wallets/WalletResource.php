<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets;

use App\Filament\Resources\Wallets\Pages\CreateWallet;
use App\Filament\Resources\Wallets\Pages\EditWallet;
use App\Filament\Resources\Wallets\Pages\ListWallets;
use App\Filament\Resources\Wallets\Pages\ViewWallet;
use App\Filament\Resources\Wallets\RelationManagers\TransactionsRelationManager;
use App\Filament\Resources\Wallets\Schemas\WalletForm;
use App\Filament\Resources\Wallets\Schemas\WalletInfolist;
use App\Filament\Resources\Wallets\Tables\WalletsTable;
use App\Models\Wallet;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return WalletForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WalletInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalletsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWallets::route('/'),
            'create' => CreateWallet::route('/create'),
            'view' => ViewWallet::route('/{record}'),
            'edit' => EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('Wallet');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Wallets');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Financial Operations');
    }

    //    public static function canCreate(): bool
    //    {
    //        return false;
    //    }
}
