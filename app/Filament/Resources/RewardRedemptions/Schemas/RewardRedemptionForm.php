<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Schemas;

use App\Enums\RewardRedemptionStatus;
use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RewardRedemptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        Grid::make()
                            ->columns(3)
                            ->schema([
                                Select::make('reward_id')
                                    ->label(__('Product Name'))
                                    ->relationship('reward', 'name_' . app()->getLocale())
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('user_id')
                                    ->label(__('User'))
                                    ->relationship(
                                        name: 'user',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->whereNot('role', UserRole::ADMIN)
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                ToggleButtons::make('status')
                                    ->label(__('Request Status'))
                                    ->options(RewardRedemptionStatus::class)
                                    ->default(RewardRedemptionStatus::NEW)
                                    ->grouped()
                                    ->required(),
                            ]),

                        Textarea::make('redemption_instructions')
                            ->label(__('Redemption Instructions'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
