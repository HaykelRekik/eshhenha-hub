<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Pages;

use App\Filament\Resources\ShippingCompanies\Schemas\Components\Infolist\BankCredentialsSection;
use App\Filament\Resources\ShippingCompanies\Schemas\Components\Infolist\CompanyDetailsSection;
use App\Filament\Resources\ShippingCompanies\Schemas\Components\Infolist\ShippingAndInsuranceSection;
use App\Filament\Resources\ShippingCompanies\ShippingCompanyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewShippingCompany extends ViewRecord
{
    protected static string $resource = ShippingCompanyResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                CompanyDetailsSection::make(),
                ShippingAndInsuranceSection::make(),
                BankCredentialsSection::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
