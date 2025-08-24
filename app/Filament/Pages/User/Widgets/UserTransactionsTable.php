<?php

declare(strict_types=1);

namespace App\Filament\Pages\User\Widgets;

use App\Filament\Resources\WalletTransactions\Tables\WalletTransactionsTable;
use App\Models\WalletTransaction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;

class UserTransactionsTable extends BaseTableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return WalletTransactionsTable::configure($table)
            ->heading(null)
            ->query($this->getUserTransactionsQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc');
    }

    protected function getUserTransactionsQuery(): Builder
    {
        return WalletTransaction::query()
            ->where('user_id', auth()->id());
    }
}
