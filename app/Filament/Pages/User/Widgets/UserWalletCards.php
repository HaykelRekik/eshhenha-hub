<?php

declare(strict_types=1);

namespace App\Filament\Pages\User\Widgets;

use App\Enums\Icons\PhosphorIcons;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class UserWalletCards extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('current_balance', $this->formatAmount(auth()->user()->wallet->balance))
                ->label(__('Current Balance'))
                ->icon(PhosphorIcons::CreditCardDuotone)
                ->extraAttributes(['class' => 'border-b-2 border-cyan-700']),

            Stat::make('loyalty_points', auth()->user()->loyalty_points . ' ' . __('point'))
                ->label(__('Loyalty Points'))
                ->icon(PhosphorIcons::MedalDuotone)
                ->extraAttributes(['class' => 'border-b-2 border-emerald-700']),
        ];
    }

    /**
     * Format the amount with new SAR currency symbol.
     */
    private function formatAmount(mixed $amount): HtmlString
    {
        $value = Number::format(number: (float) $amount, precision: 2, locale: 'en');
        $currencySymbol = view('filament.components.saudi-riyal', ['size' => '4xl'])->render();

        return new HtmlString("{$value} {$currencySymbol}");
    }
}
