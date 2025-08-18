<?php

declare(strict_types=1);

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name_ar')
                            ->required()
                            ->label(__('Name (Arabic)')),

                        TextInput::make('name_en')
                            ->required()
                            ->label(__('Name (English)')),

                        TextInput::make('name_ur')
                            ->required()
                            ->label(__('Name (Urdu)')),

                        Select::make('country_id')
                            ->label(__('Country Name'))
                            ->required()
                            ->preload()
                            ->searchable(['name_ar', 'name_en', 'name_ur'])
                            ->relationship(name: 'country', titleAttribute: 'name_' . app()->getLocale()),

                        ToggleButtons::make('is_active')
                            ->label(__('Status'))
                            ->default(true)
                            ->boolean(
                                trueLabel: __('Active'),
                                falseLabel: __('Inactive'),
                            )
                            ->required(),
                    ]),
            ]);
    }
}
