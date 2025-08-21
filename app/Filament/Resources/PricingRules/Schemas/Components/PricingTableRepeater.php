<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\InternationalPriceInput;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\LocalPriceInput;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\WeightFromInput;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\WeightToInput;
use Filament\Forms\Components\Repeater;

class PricingTableRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('pricing_table')
            ->columns(4)
            ->columnSpanFull()
            ->minItems(1)
            ->cloneable()
            ->label(__('Pricing Table'))
            ->components([
                WeightFromInput::make(),
                WeightToInput::make(),
                LocalPriceInput::make(),
                InternationalPriceInput::make(),
            ]);
    }
}
