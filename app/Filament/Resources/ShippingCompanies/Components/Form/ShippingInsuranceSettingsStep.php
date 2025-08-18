<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies\Components\Form;

use App\Enums\ShippingCompanyInsuranceType;
use App\Enums\ShippingRange;
use App\Models\ShippingCompany;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

final class ShippingInsuranceSettingsStep
{
    public static function make(): Step
    {
        return Step::make('shipping_insurance_settings')
            ->label(__('Shipping And Insurance Settings'))
            ->icon('phosphor-package-duotone')
            ->columns(2)
            ->schema([
                Fieldset::make()
                    ->label(__('Shipping'))
                    ->schema([

                        Select::make('delivery_zones')
                            ->label(__('Delivery zones'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->required()
                            ->relationship(
                                name: 'deliveryZones',
                                titleAttribute: 'name_' . app()->getLocale(),
                                modifyQueryUsing: fn (Builder $query) => $query->with('country:id,name_' . app()->getLocale())
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                $locale = app()->getLocale();

                                return $record->country->{'name_' . $locale} . ' - ' . $record->{'name_' . $locale};
                            }),

                        Grid::make()
                            ->columns(3)
                            ->schema([
                                Select::make('shipping_range')
                                    ->label(__('Shipping Range'))
                                    ->required()
                                    ->options(ShippingRange::class),

                                ToggleButtons::make('has_home_pickup')
                                    ->label(__('Enable the home pickup option ?'))
                                    ->dehydrated(false)
                                    ->grouped()
                                    ->boolean()
                                    ->afterStateHydrated(function (Set $set, ?ShippingCompany $record): void {
                                        if ( ! $record instanceof ShippingCompany) {
                                            return;
                                        }
                                        $set('has_home_pickup', $record->has_home_pickup);
                                    }),

                                TextInput::make('home_pickup_cost')
                                    ->label(__('Home Pickup Cost'))
                                    ->saudiRiyal()
                                    ->visibleJs(
                                        <<<'JS'
                                            $get('has_home_pickup') == true
                                        JS
                                    )
                                    ->required(fn (Get $get): bool => (bool) $get('has_home_pickup')),
                            ]),
                    ]),

                Fieldset::make()
                    ->label(__('Insurance'))
                    ->schema([
                        ToggleButtons::make('insurance_type')
                            ->label(__('Insurance Type'))
                            ->options(ShippingCompanyInsuranceType::class)
                            ->default(ShippingCompanyInsuranceType::PERCENTAGE)
                            ->inline()
                            ->required()
                            ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                if ($get('insurance_value')) {
                                    $currentValue = (float) $get('insurance_value');

                                    if (ShippingCompanyInsuranceType::PERCENTAGE === $state && $currentValue > 100) {
                                        $set('insurance_value', 100);
                                    }
                                }
                            })
                            ->reactive(),

                        TextInput::make('insurance_value')
                            ->label(__('Insurance Value'))
                            ->required()
                            ->maxLength(null)
                            ->numeric()
                            ->suffix(function (Get $get) {
                                static $lastType = null;
                                static $lastSuffix = null;

                                $currentType = $get('insurance_type');

                                if ($lastType !== $currentType) {
                                    $lastType = $currentType;
                                    $lastSuffix = ShippingCompanyInsuranceType::PERCENTAGE === $currentType ? '%' : new HtmlString(view('filament.components.saudi-riyal'));
                                }

                                return $lastSuffix;
                            })
                            ->maxValue(fn (Get $get): ?int => ShippingCompanyInsuranceType::PERCENTAGE === $get('insurance_type') ? 100 : null)
                            ->minValue(1)
                            ->reactive(),
                    ]),
            ]);
    }
}
