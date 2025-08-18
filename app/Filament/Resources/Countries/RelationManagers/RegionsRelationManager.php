<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RegionsRelationManager extends RelationManager
{
    protected static string $relationship = 'regions';

    public static function getModelLabel(): ?string
    {
        return __('region');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Regions management');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')
                    ->label(__('Name (Arabic)'))
                    ->required(),

                TextInput::make('name_en')
                    ->label(__('Name (English)'))
                    ->required(),

                TextInput::make('name_ur')
                    ->label(__('Name (Urdu)'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('regions')
            ->columns([
                TextColumn::make('name_ar')
                    ->searchable()
                    ->label(__('Name (Arabic)')),

                TextColumn::make('name_en')
                    ->searchable()
                    ->label(__('Name (Arabic)')),

                TextColumn::make('name_ur')
                    ->searchable()
                    ->label(__('Name (Arabic)')),

                TextColumn::make('cities_count')
                    ->counts('cities')
                    ->suffix(' ' . __('city'))
                    ->label(__('Cities Count')),

                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),
            ])
            ->filters([

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
