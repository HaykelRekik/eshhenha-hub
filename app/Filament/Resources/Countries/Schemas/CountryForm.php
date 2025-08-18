<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CountryForm
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
                        TextInput::make('iso_code')
                            ->label(__('ISO Code'))
                            ->required(),

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
