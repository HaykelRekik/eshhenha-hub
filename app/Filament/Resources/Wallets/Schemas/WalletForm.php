<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wallets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WalletForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')->nullable()->relationship('user', 'name'),
                        TextInput::make('balance')->numeric(),
                        DateTimePicker::make('last_operation_at')->time(false),
                        Toggle::make('is_locked'),
                        TextInput::make('lock_reason')->label('Lock_reason'),
                    ]),

            ]);
    }
}
