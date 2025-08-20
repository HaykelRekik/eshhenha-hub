<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\PricingRules\PricingRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListPricingRules extends ListRecords
{
    protected static string $resource = PricingRuleResource::class;

    protected static ?int $navigationSort = 3;

    public function getTabs(): array
    {
        return [
            'companies' => Tab::make(__('Companies Rules'))
                ->icon(UserRole::COMPANY->getIcon())
                ->modifyQueryUsing(fn ($query) => $query->whereNotNull('company_id')),

            'customers' => Tab::make(__('Customers Rules'))
                ->icon(UserRole::USER->getIcon())
                ->modifyQueryUsing(fn ($query) => $query->whereNull('company_id')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
