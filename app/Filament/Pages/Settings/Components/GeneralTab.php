<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

final class GeneralTab
{
    public static function make(): Tab
    {
        return Tab::make('General')
            ->components([
                TextInput::make('brand_name')
                    ->required(),
            ]);
    }
}
