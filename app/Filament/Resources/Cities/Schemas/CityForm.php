<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cities\Schemas;

use App\Models\Country;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name_ar')
                            ->label(__('Name (Arabic)'))
                            ->required(),

                        TextInput::make('name_en')
                            ->label(__('Name (English)'))
                            ->required(),

                        TextInput::make('name_ur')
                            ->label(__('Name (Urdu)'))
                            ->required(),

                        Select::make('country_id')
                            ->label(__('Country'))
                            ->required()
                            ->options(
                                options: Country::where('is_active', true)->pluck('name_' . app()->getLocale(), 'id'),
                            )
                            ->preload()
                            ->dehydrated(false)
                            ->searchable()
                            ->live(onBlur: true),

                        Select::make('region_id')
                            ->label(__('Region'))
                            ->required()
                            ->relationship(
                                name: 'region',
                                titleAttribute: 'name_' . app()->getLocale(),
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->when(
                                    $get('country_id'),
                                    fn ($query, $countryId) => $query->where('country_id', $countryId)->where('is_active', true),
                                )
                            )
                            ->preload()
                            ->searchable()
                            ->disabled(fn (Get $get): bool => ! $get('country_id'))
                            ->dehydrated(fn (Get $get): bool => (bool) $get('country_id')),

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
