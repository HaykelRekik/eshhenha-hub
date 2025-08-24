<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RewardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Product Image'))
                    ->imageHeight('3rem'),

                TextColumn::make('name_' . app()->getLocale())
                    ->label(__('Product Name'))
                    ->weight(FontWeight::Medium)
                    ->searchable(),

                TextColumn::make('supplier_name')
                    ->label(__('Supplier Name'))
                    ->placeholder(__('Not specified')),

                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric(locale: 'en'),

                TextColumn::make('required_points')
                    ->label(__('Required Points'))
                    ->numeric(locale: 'en')
                    ->suffix(' ' . __('point')),

                TextColumn::make('reward_redemptions_count')
                    ->counts('rewardRedemptions')
                    ->label(__('Redemptions'))
                    ->suffix(' ' . __('request')),

                IconColumn::make('is_active')
                    ->label(__('Availability'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sinceTooltip(),

            ])
            ->filters([

            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([

            ]);
    }
}
