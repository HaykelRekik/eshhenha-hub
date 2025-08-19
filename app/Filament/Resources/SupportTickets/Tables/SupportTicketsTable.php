<?php

declare(strict_types=1);

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Enums\UserRole;
use App\Models\SupportTicket;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->description(fn (SupportTicket $record): string => $record->user->phone_number)
                    ->icon('phosphor-user')
                    ->searchable(),

                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sinceTooltip(),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label(__('User'))
                    ->native(false)
                    ->searchable()
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereNot('role', UserRole::ADMIN),
                    )
                    ->preload(),
            ])
            ->deferFilters()
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
