<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Components\Infolist;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

final class CompanyDetailsSection
{
    public static function make(): Section
    {
        return Section::make(__('Company Details'))
            ->icon('phosphor-identification-card-duotone')
            ->columns(4)
            ->collapsible()
            ->components([
                Grid::make()
                    ->columns(1)
                    ->components([
                        ImageEntry::make('logo')
                            ->label(__('Company Logo'))
                            ->imageHeight(120)
                            ->hiddenLabel()
                            ->visibility('public')
                            ->circular(),
                    ])->columnSpan(1),

                Grid::make()
                    ->columns(4)
                    ->components([
                        TextEntry::make('name')
                            ->weight(FontWeight::Medium)
                            ->label(__('Company Name')),

                        TextEntry::make('email')
                            ->label(__('Email Address'))
                            ->icon('heroicon-m-envelope'),

                        PhoneEntry::make('phone_number')
                            ->label(__('Phone Number'))
                            ->icon('heroicon-m-phone'),

                        IconEntry::make('is_active')
                            ->label(__('Status'))
                            ->boolean(),

                        TextEntry::make('address.full_address')
                            ->label(__('Full Address'))
                            ->columnSpanFull(),
                    ])->columnSpan(3),
            ]);
    }
}
