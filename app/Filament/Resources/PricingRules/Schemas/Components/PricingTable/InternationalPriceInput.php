<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components\PricingTable;

use Filament\Forms\Components\TextInput;

class InternationalPriceInput
{
    public static function make(): TextInput
    {
        return TextInput::make('international_price_per_kg')
            ->label(__('International shipment price'))
            ->required()
            ->saudiRiyal()
            ->hint(__('Price Per KG'))
            ->numeric()
            ->minValue(0.00)
            ->maxLength(null);
    }
}
