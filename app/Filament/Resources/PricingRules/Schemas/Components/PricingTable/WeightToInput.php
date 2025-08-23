<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components\PricingTable;

use Filament\Forms\Components\TextInput;

class WeightToInput
{
    public static function make(): TextInput
    {
        return TextInput::make('weight_to')
            ->label(__('Weight To'))
            ->suffix(__('KG'))
            ->required()
            ->numeric()
            ->gt('weight_from')
            ->minValue(0.00)
            ->maxLength(null);
    }
}
