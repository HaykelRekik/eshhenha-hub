<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All Users'))
                ->icon(PhosphorIcons::UsersDuotone),

            'admins' => Tab::make(__('Admins'))
                ->icon(UserRole::ADMIN->getIcon())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->role(UserRole::ADMIN)),

            'companies' => Tab::make(__('Companies'))
                ->icon(UserRole::COMPANY->getIcon())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->role(UserRole::COMPANY)),

            'users' => Tab::make(__('Customers'))
                ->icon(UserRole::USER->getIcon())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->role(UserRole::USER)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
