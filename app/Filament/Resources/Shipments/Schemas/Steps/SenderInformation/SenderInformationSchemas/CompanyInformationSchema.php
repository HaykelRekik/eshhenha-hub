<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas\Steps\SenderInformation\SenderInformationSchemas;

use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Schemas\Steps\RecipientInformation\RecipientInformationStep;
use App\Models\Company;
use App\Models\Warehouse;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class CompanyInformationSchema
{
    public static function make(): array
    {
        return [
            Select::make('company_id')
                ->label(__('Company'))
                ->options(function () {
                    if (UserRole::COMPANY === auth()->user()->role) {
                        return Company::where('user_id', auth()->user()->id)->get()->pluck('name', 'id');
                    }

                    return Company::where('is_active', true)
                        ->get()
                        ->pluck('name', 'id');

                })
                ->default(fn () => UserRole::COMPANY === auth()->user()->role ? auth()->user()->company()->sole()->id : null)
                ->disabled(fn (): bool => UserRole::COMPANY === auth()->user()->role)
                ->searchable()
                ->preload()
                ->live()
                ->partiallyRenderComponentsAfterStateUpdated([
                    'warehouse_id',
                    'sender_street',
                    'sender_city_id',
                    'sender_region_id',
                    'sender_country_id',
                    'sender_zip_code',
                ])
                ->visibleJs(
                    <<<'JS'
                                            $get('sender_type') === 'company'
                                            JS
                )
                ->required(fn (Get $get): bool => 'company' === $get('sender_type'))
                ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                    $set('senderable_id', (int) ($state));
                    $set('warehouse_id', null);
                    $set('sender_street', null);
                    $set('sender_city_id', null);
                    $set('sender_region_id', null);
                    $set('sender_country_id', null);
                    $set('sender_zip_code', null);
                    if ($get('recipient_mode') && 'existing' === $get('recipient_mode')) {
                        RecipientInformationStep::resetFields($set);
                    }

                }),

            // Select Company's Warehouse
            Select::make('warehouse_id')
                ->label(__('Warehouse'))
                ->options(function (Get $get) {
                    $companyId = $get('company_id');
                    if ( ! $companyId) {
                        return collect();
                    }

                    return Warehouse::where('company_id', $companyId)
                        ->get()
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->preload()
                ->live()
                ->partiallyRenderComponentsAfterStateUpdated([
                    'sender_street',
                    'sender_city_id',
                    'sender_region_id',
                    'sender_country_id',
                    'sender_zip_code',
                ])
                ->visibleJs(
                    <<<'JS'
                                            $get('sender_type') === 'company' && $get('company_id')
                                            JS
                )
                ->required(fn (Get $get): bool => 'company' === $get('sender_type'))
                ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                    if ('company' === $get('sender_type')) {
                        $companyId = $get('company_id');
                        if ($companyId && $state) {
                            $company = Company::find($companyId);
                            $warehouse = Warehouse::find($state);
                            if ($company && $warehouse) {
                                $warehouseAddress = $warehouse->addresses()->first();
                                $set('sender_street', $warehouseAddress->street);
                                $set('sender_city_id', $warehouseAddress->city?->id);
                                $set('sender_region_id', $warehouseAddress->region?->id);
                                $set('sender_country_id', $warehouseAddress->country?->id);
                                $set('sender_zip_code', $warehouseAddress->zip_code);
                            }
                        }
                    }
                    if ($get('recipient_mode') && 'existing' === $get('recipient_mode')) {
                        RecipientInformationStep::resetFields($set);
                    }
                }),
        ];
    }
}
