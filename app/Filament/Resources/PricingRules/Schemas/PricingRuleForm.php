<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PricingRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->columns(3)
                            ->schema([
                                ToggleButtons::make('pricing_rule_type')
                                    ->label(__('Pricing rule for'))
                                    ->required()
                                    ->options([
                                        'companies' => __('Companies'),
                                        'customers' => __('Customers'),
                                    ])
                                    ->icons([
                                        'companies' => UserRole::COMPANY->getIcon(),
                                        'customers' => UserRole::USER->getIcon(),
                                    ])
                                    ->grouped()
                                    ->dehydrated(false)
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn (Set $set, $state): mixed => 'customers' === $state ? $set('company_id', null) : null)
                                    ->afterStateHydrated(function (Set $set, ?Model $record): void {
                                        if ( ! $record instanceof Model) {
                                            return;
                                        }
                                        $set('pricing_rule_type', null === $record->company_id ? 'customers' : 'companies');
                                    }),

                                Select::make('shipping_company_id')
                                    ->label(__('Shipping Company'))
                                    ->relationship('shippingCompany', 'name')
                                    ->required()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn (Set $set): mixed => $set('company_id', null)),

                                Select::make('company_id')
                                    ->label(__('Company'))
                                    ->relationship(
                                        name: 'company',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->whereRelation('contracts', 'shipping_company_id', $get('shipping_company_id'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Get $get): bool => 'companies' !== $get('pricing_rule_type'))
                                    ->required(fn (Get $get): bool => 'companies' === $get('pricing_rule_type')),
                            ]),
                        TextInput::make('weight_from')
                            ->label(__('Weight From'))
                            ->suffix(__('KG'))
                            ->required()
                            ->numeric()
                            ->minValue(0.00)
                            ->maxLength(null),
                        TextInput::make('weight_to')
                            ->label(__('Weight To'))
                            ->suffix(__('KG'))
                            ->required()
                            ->numeric()
                            ->gt('weight_from')
                            ->minValue(0.00)
                            ->maxLength(null),
                        TextInput::make('local_price_per_kg')
                            ->label(__('Local shipment price'))
                            ->required()
                            ->saudiRiyal()
                            ->hint(__('Price Per KG'))
                            ->numeric()
                            ->minValue(0.00)
                            ->maxLength(null),
                        TextInput::make('international_price_per_kg')
                            ->label(__('International shipment price'))
                            ->required()
                            ->saudiRiyal()
                            ->hint(__('Price Per KG'))
                            ->numeric()
                            ->minValue(0.00)
                            ->maxLength(null),
                    ]),
            ]);
    }
}
