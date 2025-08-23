<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: 'sender_name')
                    ->label(__('Sender Name'))
                    ->searchable(),
                TextColumn::make('sender_email')
                    ->label(__('Sender Email'))
                    ->searchable(),
                PhoneColumn::make('sender_phone_number')
                    ->label(__('Phone Number'))
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(length: 30)
                    ->searchable(),
                TextColumn::make('message')
                    ->label(__('Message'))
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([

            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('View'))
                    ->icon('phosphor-eye-bold')
                    ->schema([
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                TextEntry::make('sender_name')
                                    ->icon('phosphor-user-bold')
                                    ->label(__('Sender Name')),
                                TextEntry::make('sender_email')
                                    ->icon('phosphor-envelope-bold')
                                    ->label(__('Sender Email')),
                                PhoneEntry::make('sender_phone_number')
                                    ->icon('phosphor-phone-bold')
                                    ->label(__('Phone Number')),
                                TextEntry::make('subject')
                                    ->icon('phosphor-chat-bold')
                                    ->label(__('Subject')),
                            ]),
                        TextEntry::make('message')
                            ->icon('phosphor-chat-text-bold')
                            ->label(__('Message')),
                    ])
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
