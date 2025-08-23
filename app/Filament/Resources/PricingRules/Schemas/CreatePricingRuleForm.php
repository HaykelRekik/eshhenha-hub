<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas;

use App\Filament\Resources\PricingRules\Schemas\Components\CompanySelect;
use App\Filament\Resources\PricingRules\Schemas\Components\CustomerSelect;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingRuleTypeSelect;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTableRepeater;
use App\Filament\Resources\PricingRules\Schemas\Components\ShippingCompanySelect;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreatePricingRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        Grid::make()
                            ->columns(3)
                            ->schema([
                                PricingRuleTypeSelect::make(),
                                ShippingCompanySelect::make(),
                                CompanySelect::make(),
                                CustomerSelect::make(),
                            ]),
                        PricingTableRepeater::make(),
                    ]),
            ]);
    }
}
