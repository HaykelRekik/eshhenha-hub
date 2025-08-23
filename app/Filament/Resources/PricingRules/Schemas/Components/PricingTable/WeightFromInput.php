<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components\PricingTable;

use Filament\Forms\Components\TextInput;

class WeightFromInput
{
    public static function make(): TextInput
    {
        return TextInput::make('weight_from')
            ->label(__('Weight From'))
            ->suffix(__('KG'))
            ->required()
            ->numeric()
            ->minValue(0.00)
            ->maxLength(null);
    }
}
