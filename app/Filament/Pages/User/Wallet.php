<?php

declare(strict_types=1);

namespace App\Filament\Pages\User;

use App\Enums\UserRole;
use App\Filament\Pages\User\Actions\TopupBalanceAction;
use Filament\Pages\Page;

class Wallet extends Page
{
    protected string $view = 'filament.pages.user.wallet';

    protected static ?string $slug = 'user/wallet';

    protected function getHeaderActions(): array
    {
        return [
            TopupBalanceAction::make(),
        ];
    }

    //    public static function canAccess(): bool
    //    {
    //        return auth()->user()->hasAnyRole([UserRole::USER , UserRole::COMPANY]);
    //    }

}
