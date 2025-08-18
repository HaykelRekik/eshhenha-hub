<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

class SocialMediaTab
{
    public static function make(): Tab
    {
        return Tab::make(__('Social Media Links'))
            ->columns(2)
            ->icon('phosphor-link-duotone')
            ->schema([
                TextInput::make('social_media.facebook')
                    ->nullable()
                    ->url()
                    ->label(__('Facebook Link'))
                    ->placeholder('https://facebook.com')
                    ->prefixIcon('phosphor-facebook-logo-duotone'),

                TextInput::make('social_media.x')
                    ->nullable()
                    ->url()
                    ->label(__('X (Twitter) Link'))
                    ->placeholder('https://x.com')
                    ->prefixIcon('phosphor-x-logo-duotone'),

                TextInput::make('social_media.snapchat')
                    ->nullable()
                    ->url()
                    ->label(__('Snapchat Link'))
                    ->placeholder('https://www.snapchat.com')
                    ->prefixIcon('phosphor-snapchat-logo-duotone'),

                TextInput::make('social_media.instagram')
                    ->nullable()
                    ->url()
                    ->label(__('Instagram Link'))
                    ->placeholder('https://instagram.com')
                    ->prefixIcon('phosphor-instagram-logo-duotone'),

                TextInput::make('social_media.youtube')
                    ->nullable()
                    ->url()
                    ->label(__('Youtube Link'))
                    ->placeholder('https://youtube.com')
                    ->prefixIcon('phosphor-youtube-logo-duotone'),
            ]);
    }
}
