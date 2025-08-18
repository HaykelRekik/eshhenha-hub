<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Schemas;

use App\Filament\Resources\ShippingCompanies\Components\Form\BankInformationStep;
use App\Filament\Resources\ShippingCompanies\Components\Form\CompanyDetailsStep;
use App\Filament\Resources\ShippingCompanies\Components\Form\ShippingInsuranceSettingsStep;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

class ShippingCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    CompanyDetailsStep::make(),
                    ShippingInsuranceSettingsStep::make(),
                    BankInformationStep::make(),
                ]),
            ]);
    }
}
