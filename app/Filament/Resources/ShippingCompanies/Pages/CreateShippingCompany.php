<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Pages;

use App\Filament\Resources\ShippingCompanies\ShippingCompanyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingCompany extends CreateRecord
{
    protected static string $resource = ShippingCompanyResource::class;
}
