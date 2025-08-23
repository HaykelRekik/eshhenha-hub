<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Pages;

use App\Filament\Resources\ShippingCompanies\ShippingCompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShippingCompany extends EditRecord
{
    protected static string $resource = ShippingCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
