<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Wallets\WalletResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;

    public function getTabs(): array
    {
        return [
            'companies' => Tab::make(__('Companies Wallets'))
                ->icon(UserRole::COMPANY->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('user', 'role', UserRole::COMPANY)),

            'customers' => Tab::make(__('Customers Wallets'))
                ->icon(UserRole::USER->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('user', 'role', UserRole::USER)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
