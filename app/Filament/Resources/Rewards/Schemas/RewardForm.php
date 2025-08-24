<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Schemas;

use App\Enums\Icons\PhosphorIcons;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RewardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('name_ar')
                            ->label(__('Name (Arabic)'))
                            ->required(),
                        TextInput::make('name_en')
                            ->label(__('Name (Arabic)'))
                            ->required(),
                        Textarea::make('description_ar')
                            ->label(__('Description (Arabic)'))
                            ->maxLength(1000),
                        Textarea::make('description_en')
                            ->label(__('Description (English)'))
                            ->maxLength(1000),
                        FileUpload::make('image')
                            ->image()
                            ->directory('rewards')
                            ->columnSpanFull()
                            ->maxSize(1024)
                            ->required(),

                        Grid::make()
                            ->columns(3)
                            ->components([
                                TextInput::make('quantity')
                                    ->label(__('Available Quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(1),

                                TextInput::make('required_points')
                                    ->label(__('Required Points'))
                                    ->required()
                                    ->suffix(__('Points'))
                                    ->minValue(0)
                                    ->numeric(),

                                ToggleButtons::make('is_active')
                                    ->label(__('Availability'))
                                    ->default(true)
                                    ->boolean(
                                        trueLabel: __('Available'),
                                        falseLabel: __('Unavailable'),
                                    )
                                    ->required(),
                            ]),

                        TextInput::make('supplier_name')
                            ->label(__('Supplier Name')),

                        TextInput::make('external_identifier')
                            ->label(__('External Identifier'))
                            ->hintIcon(icon: PhosphorIcons::InfoDuotone, tooltip: __('This field is used to link the reward to the external system.')),
                    ]),
            ]);
    }
}
