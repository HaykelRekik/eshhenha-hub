<?php

declare(strict_types=1);

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Image')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->placeholder(__('Not specified'))
                    ->searchable(['title_ar', 'title_en', 'title_ur']),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->placeholder(__('Not specified'))
                    ->searchable(['description_ar', 'description_en', 'description_ur']),

                IconColumn::make('link')
                    ->icon(Heroicon::OutlinedLink)
                    ->color('info')
                    ->placeholder(__('Not specified'))
                    ->label(__('Link')),

                TextColumn::make('clicks_count')
                    ->label(__('Clicks count'))
                    ->suffix(' ' . __('Clicks')),

                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
