<?php

declare(strict_types=1);

namespace App\Filament\Pages\Rewards\Actions;

use App\Enums\Icons\PhosphorIcons;
use App\Models\Reward;
use Filament\Actions\Action;

class RequestRewardAction
{
    public static function make(): Action
    {
        return Action::make('request_reward')
            ->color('success')
            ->button()
            ->outlined()
            ->extraAttributes(['class' => 'w-full'])
            ->label(__('Request Reward'))
            ->icon(PhosphorIcons::ShoppingBagDuotone)
            ->disabled(fn (Reward $record): bool => $record->required_points > auth()->user()->loyalty_points);
    }
}
