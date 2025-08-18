<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'cities';

    protected static bool $isLazy = false;

    public static function getModelLabel(): ?string
    {
        return __('city');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Cities management');
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

                Select::make('region_id')
                    ->label(__('Region'))
                    ->required()
                    ->relationship(
                        name: 'region',
                        titleAttribute: 'name_' . app()->getLocale(),
                        modifyQueryUsing: fn (Builder $query) => $query->where('country_id', $this->getOwnerRecord()->id)
                    )
                    ->preload()
                    ->searchable(['name_ar', 'name_en', 'name_ur']),

                ToggleButtons::make('is_active')
                    ->label(__('Status'))
                    ->default(true)
                    ->boolean(
                        trueLabel: __('Active'),
                        falseLabel: __('Inactive'),
                    )
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('cities')
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

                TextColumn::make('region.name_' . app()->getLocale())
                    ->label(__('Region')),

                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label(__('Region'))
                    ->relationship('region', 'name_' . app()->getLocale())
                    ->preload()
                    ->searchable(['name_ar', 'name_en', 'name_ur']),
            ])
            ->deferFilters()
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
