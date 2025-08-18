<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Pages;

use App\Filament\Resources\ShippingCompanies\Actions\ManagePricingRulesAction;
use App\Filament\Resources\ShippingCompanies\ShippingCompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShippingCompanies extends ListRecords
{
    protected static string $resource = ShippingCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ManagePricingRulesAction::make(),
            CreateAction::make(),
        ];
    }
}
