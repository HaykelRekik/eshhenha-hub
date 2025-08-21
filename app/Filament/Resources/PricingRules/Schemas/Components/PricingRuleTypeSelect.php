<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Operation;

class PricingRuleTypeSelect
{
    public static function make(): Select
    {
        return Select::make('type')
            ->label(__('Pricing rule for'))
            ->options(PricingRuleType::class)
            ->required()
            ->hiddenOn(Operation::Edit)
            ->partiallyRenderAfterStateUpdated(true) // enables partial rerendering
            ->live()
            ->afterStateUpdatedJs(
                <<<'JS'
                $set('shipping_company_id' , null);
                $set('company_id' , null);
                $set('user_id' , null);
                JS
            );
    }
}
