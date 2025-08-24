<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RewardRedemptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextEntry::make('reward.name')
                            ->label(__('Requested Product')),
                        TextEntry::make('user.name')
                            ->label(__('User')),

                        TextEntry::make('user.loyalty_points')
                            ->label(__('User Loyalty Points'))
                            ->suffix(' ' . __('point')),

                        TextEntry::make('reward.required_points')
                            ->label(__('Required Points'))
                            ->suffix(' ' . __('point')),

                        TextEntry::make('reference')
                            ->label(__('Reference'))
                            ->prefix('#'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->label(__('Request date'))
                            ->dateTime(),
                    ]),
            ]);
    }
}
