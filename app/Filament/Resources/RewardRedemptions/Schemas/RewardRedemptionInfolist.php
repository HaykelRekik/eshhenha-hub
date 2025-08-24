<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Schemas;

use App\Models\RewardRedemption;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RewardRedemptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(4)
                    ->components([
                        TextEntry::make('reward.name')
                            ->label(__('Requested Product')),

                        TextEntry::make('user.name')
                            ->label(__('User')),

                        TextEntry::make('reference')
                            ->label(__('Request Reference'))
                            ->prefix('#'),

                        TextEntry::make('status')
                            ->label(__('Request Status'))
                            ->badge(),

                        Grid::make()
                            ->columns(3)
                            ->components([
                                TextEntry::make('user.loyalty_points')
                                    ->label(__('User Loyalty Points'))
                                    ->suffix(' ' . __('point')),

                                TextEntry::make('reward.required_points')
                                    ->label(__('Required Points'))
                                    ->suffix(' ' . __('point'))
                                    ->color(fn (RewardRedemption $record): string => $record->reward->required_points > $record->user->loyalty_points ? 'danger' : 'gray'),

                                TextEntry::make('created_at')
                                    ->label(__('Request date'))
                                    ->dateTime('d M Y , h:i A')
                                    ->sinceTooltip(),
                            ]),
                    ]),
            ]);
    }
}
