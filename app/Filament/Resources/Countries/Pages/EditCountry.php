<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Resources\Countries\CountryResource;
use App\Traits\RedirectToIndex;
use Filament\Resources\Pages\EditRecord;

class EditCountry extends EditRecord
{
    use RedirectToIndex;

    protected static string $resource = CountryResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('Country details');
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
