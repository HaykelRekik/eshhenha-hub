<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Warehouses\Schemas\WarehouseForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class WarehousesRelationManager extends RelationManager
{
    protected static string $relationship = 'warehouses';

    protected static bool $isLazy = false;

    public static function getModelLabel(): ?string
    {
        return __('warehouse');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Warehouses management');
    }

    public function form(Schema $schema): Schema
    {
        return WarehouseForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading(__('Warehouses'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Warehouse'))
                    ->weight(FontWeight::Medium)
                    ->searchable(),

                TextColumn::make('responsible_name')
                    ->label(__('Responsible'))
                    ->searchable(),

                PhoneColumn::make('responsible_phone_number')
                    ->label(__('Responsible phone number'))
                    ->searchable(),

                TextColumn::make('address.full_address')
                    ->label(__('Address'))
                    ->limit(50),
            ])
            ->filters([

            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->label(__('Add a new warehouse')),

            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
