<?php

declare(strict_types=1);

namespace App\Filament\Pages\Rewards;

use App\Enums\UserRole;
use App\Filament\Pages\Rewards\Actions\RequestRewardAction;
use App\Models\Reward;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ExchangeOptionsStore extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.rewards.exchange-options-store';

    protected static ?string $slug = 'rewards/options';

    //    public static function canAccess(): bool
    //    {
    //        return auth()->user()->hasAnyRole([UserRole::USER, UserRole::COMPANY]);
    //    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Reward::query()->where('is_active', true))
            ->columns([
                Grid::make()
                    ->columns(1)
                    ->components([
                        ImageColumn::make('image')
                            ->imageHeight('150px')
                            ->imageWidth('100%')
                            ->circular()
                            ->extraImgAttributes(['class' => 'mx-auto w-full h-full rounded-lg overflow-hidden', 'loading' => 'lazy']),

                        TextColumn::make('name_' . app()->getLocale())
                            ->weight(FontWeight::SemiBold)
                            ->extraAttributes(['class' => 'mt-4'])
                            ->size(TextSize::Medium)
                            ->searchable(['name_ar', 'name_en']),

                        TextColumn::make('description_' . app()->getLocale())
                            ->limit(70)
                            ->extraAttributes(['class' => 'mt-2']),
                    ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->paginated(false)
            ->recordActions([
                RequestRewardAction::make(),
            ]);
    }
}
