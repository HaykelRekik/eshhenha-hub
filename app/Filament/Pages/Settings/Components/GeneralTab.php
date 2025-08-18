<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

class GeneralTab
{
    public static function make(): Tab
    {
        return Tab::make('General')
            ->schema([
                TextInput::make('brand_name')
                    ->required(),
            ]);
    }
}
