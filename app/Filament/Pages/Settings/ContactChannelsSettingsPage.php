<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Filament\Pages\Settings\Components\ContactInformationTab;
use App\Filament\Pages\Settings\Components\SocialMediaTab;
use App\Settings\ContactChannelsSettings;
use BackedEnum;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ContactChannelsSettingsPage extends SettingsPage
{
    protected static ?string $slug = 'contact-channels';

    protected static string|null|BackedEnum $navigationIcon = null;

    protected static ?int $navigationSort = 3;

    protected static string $settings = ContactChannelsSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Contact Channels Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public function getTitle(): string
    {
        return __('Contact Channels Settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()

                    ->components([
                        SocialMediaTab::make(),
                        ContactInformationTab::make(),
                    ]),
            ]);
    }
}
