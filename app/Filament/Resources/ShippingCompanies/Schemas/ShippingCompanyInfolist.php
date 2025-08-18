<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Schemas;

use App\Filament\Resources\ShippingCompanies\Components\Infolist\BankCredentialsSection;
use App\Filament\Resources\ShippingCompanies\Components\Infolist\CompanyDetailsSection;
use App\Filament\Resources\ShippingCompanies\Components\Infolist\ShippingAndInsuranceSection;
use Filament\Schemas\Schema;

class ShippingCompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CompanyDetailsSection::make(),
                ShippingAndInsuranceSection::make(),
                BankCredentialsSection::make(),
            ]);
    }
}
