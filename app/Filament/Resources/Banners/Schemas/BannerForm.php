<?php

declare(strict_types=1);

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('Image'))
                            ->image()
                            ->directory('banners')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('title_ar')
                            ->label(__('Title (Arabic)'))
                            ->nullable(),

                        TextInput::make('title_en')
                            ->label(__('Title (English)'))
                            ->nullable(),

                        TextInput::make('title_ur')
                            ->label(__('Title (Urdu)'))
                            ->nullable(),

                        Textarea::make('description_ar')
                            ->label(__('Description (Arabic)'))
                            ->nullable()
                            ->autosize(),

                        Textarea::make('description_en')
                            ->label(__('Description (English)'))
                            ->nullable()
                            ->autosize(),

                        Textarea::make('description_ur')
                            ->label(__('Description (Urdu)'))
                            ->nullable()
                            ->autosize(),

                        TextInput::make('link')
                            ->label(__('Link'))
                            ->nullable()
                            ->url()
                            ->prefixIcon(Heroicon::OutlinedLink)
                            ->columnSpan(2)
                            ->helperText(__('The user will be redirected to this link after clicking on the banner.')),

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
