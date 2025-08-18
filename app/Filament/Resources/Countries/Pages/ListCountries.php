<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Resources\Cities\CityResource;
use App\Filament\Resources\Countries\CountryResource;
use App\Filament\Resources\Regions\RegionResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ActionGroup::make([
                Action::make('Regions')
                    ->label(__('Regions management'))
                    ->icon(Heroicon::OutlinedMap)
                    ->url(RegionResource::getUrl('index')),

                Action::make('Cities')
                    ->label(__('Cities management'))
                    ->icon(Heroicon::OutlinedBuildingOffice)
                    ->url(CityResource::getUrl('index')),
            ])->color('dark'),
        ];
    }
}
