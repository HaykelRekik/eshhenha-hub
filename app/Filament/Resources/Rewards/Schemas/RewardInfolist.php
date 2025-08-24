<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class RewardInfolist
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                Section::make()
                    ->columns(5)
                    ->components([
                        Grid::make()
                            ->columns(1)
                            ->components([
                                ImageEntry::make('image')
                                    ->imageHeight(120)
                                    ->hiddenLabel()
                                    ->visibility('public'),
                            ])->columnSpan(1),

                        Grid::make()
                            ->columns(4)
                            ->components([
                                TextEntry::make('name')
                                    ->weight(FontWeight::Medium)
                                    ->label(__('Product name')),

                                TextEntry::make('supplier_name')
                                    ->label(__('Supplier name')),

                                TextEntry::make('external_identifier')
                                    ->label(__('External identifier')),

                                TextEntry::make('quantity')
                                    ->label(__('Available Quantity'))
                                    ->numeric(locale: 'en'),
                                TextEntry::make('required_points')
                                    ->numeric(locale: 'en')
                                    ->suffix(' ' . __('point')),

                                IconEntry::make('is_active')
                                    ->label(__('Availability'))
                                    ->boolean(),

                                TextEntry::make('reward_redemptions_count')
                                    ->counts('rewardRedemptions')
                                    ->label(__('Redemptions'))
                                    ->suffix(' ' . __('request')),

                                TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->dateTime('d M Y , h:i A')
                                    ->sinceTooltip(),

                                TextEntry::make('description_' . app()->getLocale())
                                    ->label(__('Product description'))
                                    ->columnSpanFull(),

                            ])->columnSpan(4),
                    ]),

            ]);

        //        return $schema
        //            ->components([
        //                Section::make()
        //                    ->components([
        //                        TextEntry::make('name_' . app()->getLocale())
        //                            ->label(__('Product name')),
        //                        TextEntry::make('description_' . app()->getLocale())
        //                            ->label(__('Product description')),
        //
        //                        TextEntry::make('supplier_name'),
        //                        TextEntry::make('external_identifier'),
        //                        TextEntry::make('quantity')
        //                            ->label(__('Available Quantity'))
        //                            ->numeric(locale: 'en'),
        //                        TextEntry::make('required_points')
        //                            ->numeric(locale: 'en')
        //                            ->suffix(' ' . __('point')),
        //                        IconEntry::make('is_active')
        //                            ->label(__('Availability'))
        //                            ->boolean(),
        //                        TextEntry::make('created_at')
        //                            ->dateTime(),
        //                        TextEntry::make('updated_at')
        //                            ->dateTime(),
        //                    ])
        //            ]);
    }
}
