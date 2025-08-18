<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Actions;

use App\Enums\Icons\PhosphorIcons;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;

class ManagePricingRulesAction
{
    public static function make(): Action
    {
        return Action::make('managePricingRules')
            ->label(__('Pricing rules management'))
            ->color(Color::Rose)
            ->outlined()
            ->icon(PhosphorIcons::BookBookmarkDuotone);
        //            ->url(PricingRuleResource::getUrl('index')),
    }
}
