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
                            ->afterStateUpdatedJs(
                                <<<'JS'

                                if ($get('insurance_value') > 100 && $get('insurance_type') === 'percentage' ) {
                                    $set('insurance_value' , 100)
                                }

                        JS
                            ),

                        /**
                         * Workaround to dynamically display the suffix symbol based on insurance type
                         */
                        TextInput::make('insurance_value')
                            ->label(__('Insurance Value'))
                            ->required()
                            ->saudiRiyal()
                            ->maxLength(null)
                            ->numeric()
                            ->visibleJs(
                                <<<'JS'
                                $get('insurance_type') === 'amount'
                            JS
                            )
                            ->minValue(1),

                        TextInput::make('insurance_value')
                            ->label(__('Insurance Value'))
                            ->required()
                            ->suffix('%')
                            ->maxLength(null)
                            ->numeric()
                            ->maxValue(100)
                            ->visibleJs(
                                <<<'JS'
                                $get('insurance_type') === 'percentage'
                            JS
                            )
                            ->minValue(1),

                    /**
                     * End Workaround to dynamically display the suffix symbol based on insurance type
                     */
                    ]),
            ]);
    }
}
