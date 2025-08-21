<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas\Components;

use App\Enums\PricingRuleType;
use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CompanySelect
{
    public static function make(): Select
    {
        return Select::make('company_id')
            ->label(__('Company'))
            ->relationship(
                name: 'company',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query, Get $get) => $query->whereRelation('contracts', 'shipping_company_id', $get('shipping_company_id'))
            )
            ->options(fn (Get $get): array|Collection => match ($get('type')) {
                PricingRuleType::COMPANY->value => Company::whereIsActive(true)->pluck('name', 'id'),
                PricingRuleType::COMPANY_SHIPPING_COMPANY->value => Company::whereIsActive(true)
                    ->whereRelation('contracts', 'shipping_company_id', $get('shipping_company_id'))
                    ->pluck('name', 'id'),
                default => [],
            })
            ->requiredIf('type', [
                PricingRuleType::COMPANY_SHIPPING_COMPANY->value,
                PricingRuleType::COMPANY->value,
            ])
            ->visibleJs(
                <<<'JS'
                $get('type') === 'company & shipping company' ||
                $get('type') === 'company'
                JS
            )
            ->searchable()
            ->preload();
    }
}
