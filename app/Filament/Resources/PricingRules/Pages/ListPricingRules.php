<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Pages;

use App\Filament\Resources\PricingRules\PricingRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPricingRules extends ListRecords
{
    protected static string $resource = PricingRuleResource::class;

    protected static ?int $navigationSort = 3;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
