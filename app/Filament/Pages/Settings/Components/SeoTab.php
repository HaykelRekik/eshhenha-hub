<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

final class SeoTab
{
    public static function make(): Tab
    {
        return Tab::make('Seo')
            ->components([
                TextInput::make('seo_title')
                    ->required(),
                TextInput::make('seo_description')
                    ->required(),
            ]);
    }
}
