<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas;

use App\Filament\Resources\PricingRules\Schemas\Components\CompanySelect;
use App\Filament\Resources\PricingRules\Schemas\Components\CustomerSelect;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingRuleTypeSelect;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\InternationalPriceInput;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\LocalPriceInput;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\WeightFromInput;
use App\Filament\Resources\PricingRules\Schemas\Components\PricingTable\WeightToInput;
use App\Filament\Resources\PricingRules\Schemas\Components\ShippingCompanySelect;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditPricingRuleForm
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
                        Grid::make()
                            ->columns(4)
                            ->components([
                                WeightFromInput::make(),
                                WeightToInput::make(),
                                LocalPriceInput::make(),
                                InternationalPriceInput::make(),
                            ]),
                    ]),
            ]);
    }
}
