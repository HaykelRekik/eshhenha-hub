<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Pages;

use App\Filament\Resources\Wallets\WalletResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewWallet extends ViewRecord
{
    protected static string $resource = WalletResource::class;

    public function infolist(Schema $schema): Schema
    {
        return parent::infolist($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            //            EditAction::make(),
        ];
    }
}
