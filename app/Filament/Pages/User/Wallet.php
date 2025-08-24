<?php

declare(strict_types=1);

namespace App\Filament\Pages\User;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\UserRole;
use App\Filament\Pages\User\Actions\BuyPointsAction;
use App\Filament\Pages\User\Actions\TopupBalanceAction;
use App\Filament\Pages\User\Widgets\UserTransactionsTable;
use App\Filament\Pages\User\Widgets\UserWalletCards;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Wallet extends Page
{
    protected static string|null|BackedEnum $navigationIcon = PhosphorIcons::Wallet;

    protected string $view = 'filament.pages.user.wallet';

    protected static ?string $slug = 'user/wallet';

    public static function getNavigationLabel(): string
    {
        return __('Wallet');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([UserRole::USER, UserRole::COMPANY]);
    }

    public function getHeading(): string|Htmlable
    {
        return __('Wallet');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('Easily top-up your wallet and purchase loyalty points for exclusive rewards.');
    }

    protected function getHeaderActions(): array
    {
        return [
            TopupBalanceAction::make(),
            BuyPointsAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserWalletCards::class,
            UserTransactionsTable::class,
        ];
    }
}
