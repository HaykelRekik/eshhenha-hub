<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Enums\UserRole;
use App\Filament\Pages\Settings\Components\BalanceToPointsTab;
use App\Filament\Pages\Settings\Components\PointsToBalanceTab;
use App\Settings\LoyaltyConversionSettings;
use BackedEnum;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class LoyaltyConversionSettingsPage extends SettingsPage
{
    protected static ?string $slug = 'loyalty-settings';

    protected static string|null|BackedEnum $navigationIcon = null;

    protected static ?int $navigationSort = 1;

    protected static string $settings = LoyaltyConversionSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Loyalty Points Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(UserRole::ADMIN);
    }

    public function getTitle(): string
    {
        return __('Loyalty Points Settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->components([
                        PointsToBalanceTab::make(),
                        BalanceToPointsTab::make(),
                    ]),
            ]);
    }
}
