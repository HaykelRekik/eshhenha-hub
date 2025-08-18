<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Filament\Pages\Settings\Components\ReferralSystemTab;
use App\Filament\Pages\Settings\Components\WelcomeBonusTab;
use App\Settings\RewardSettings;
use BackedEnum;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class RewardSettingsPage extends SettingsPage
{
    protected static ?string $slug = 'reward-settings';

    protected static string|null|BackedEnum $navigationIcon = null;

    protected static ?int $navigationSort = 2;

    protected static string $settings = RewardSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Rewards Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public function getTitle(): string
    {
        return __('Rewards Settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('Referral System'))
                    ->columnSpanFull()
                    ->components([
                        ReferralSystemTab::make(),
                        WelcomeBonusTab::make(),
                    ]),
            ]);
    }
}
