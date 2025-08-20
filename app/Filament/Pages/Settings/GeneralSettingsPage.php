<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Enums\UserRole;
use App\Filament\Pages\Settings\Components\GeneralTab;
use App\Filament\Pages\Settings\Components\SeoTab;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class GeneralSettingsPage extends SettingsPage
{
    protected static string|null|BackedEnum $navigationIcon = null;

    protected static ?int $navigationSort = 1;

    protected static string $settings = GeneralSettings::class;

    protected static ?string $slug = 'settings';

    public static function getNavigationLabel(): string
    {
        return __('General Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(UserRole::ADMIN);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->components([
                        GeneralTab::make(),
                        SeoTab::make(),
                    ]),
            ]);
    }
}
