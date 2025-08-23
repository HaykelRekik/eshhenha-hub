<?php

declare(strict_types=1);

namespace App\Filament\Resources\SupportTickets\Schemas;

use App\Enums\SupportTicketStatus;
use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('user_id')
                            ->label(__('User'))
                            ->nullable()
                            ->disabledOn('edit')
                            ->searchable()
                            ->preload()
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->whereNot('role', UserRole::ADMIN)
                            ),

                        TextInput::make('subject')
                            ->label(__('Subject'))
                            ->required(),

                        Textarea::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->autosize()
                            ->columnSpanFull(),

                        Textarea::make('response')
                            ->label(__('Response'))
                            ->hiddenOn('create')
                            ->autosize(),

                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options(SupportTicketStatus::class)
                            ->default(SupportTicketStatus::NEW),

                        //                Select::make('contact_message_id')
                        //                    ->nullable()
                        //                    ->label('Contact Message')
                        //                    ->relationship('contactMessage', 'subject')
                        //                    ->preload(),
                    ]),
            ]);
    }
}
