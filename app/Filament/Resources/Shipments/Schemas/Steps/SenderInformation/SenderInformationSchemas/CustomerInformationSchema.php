<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas\Steps\SenderInformation\SenderInformationSchemas;

use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Schemas\Steps\RecipientInformation\RecipientInformationStep;
use App\Models\Address;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class CustomerInformationSchema
{
    public static function make(): array
    {
        return [
            Select::make('customer_id')
                ->label(__('Customer'))
                ->options(function () {
                    if (UserRole::USER === auth()->user()->role) {
                        return User::where('id', auth()->user()->id)->get()->pluck('name', 'id');
                    }

                    return User::where('role', UserRole::USER)
                        ->where('is_active', true)
                        ->get()
                        ->pluck('name', 'id');

                })
                ->default(fn () => UserRole::USER === auth()->user()->role ? auth()->user()->id : null)
                ->disabled(fn (): bool => UserRole::USER === auth()->user()->role)
                ->searchable()
                ->preload()
                ->live()
                ->partiallyRenderComponentsAfterStateUpdated([
                    'address_id',
                    'sender_street',
                    'sender_city_id',
                    'sender_region_id',
                    'sender_country_id',
                    'sender_zip_code',
                ])
                ->visibleJs(
                    <<<'JS'
                                            $get('sender_type') === 'customer'
                                            JS
                )
                ->required(fn (Get $get): bool => 'customer' === $get('sender_type'))
                ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                    $set('senderable_id', (int) ($state));
                    $set('address_id', null);
                    $set('sender_street', null);
                    $set('sender_city_id', null);
                    $set('sender_region_id', null);
                    $set('sender_country_id', null);
                    $set('sender_zip_code', null);
                    if ($get('recipient_mode') && 'existing' === $get('recipient_mode')) {
                        RecipientInformationStep::resetFields($set);
                    }

                }),

            // Customer address selection
            Select::make('address_id')
                ->label(__('Address'))
                ->options(function (Get $get) {
                    $customerId = $get('customer_id');
                    if ( ! $customerId) {
                        return collect();
                    }

                    return Address::where('addressable_type', User::class)
                        ->where('addressable_id', $customerId)
                        ->get()
                        ->pluck('full_address', 'id');

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
                                            $get('sender_type') === 'customer' && $get('customer_id')
                                            JS
                )
                ->required(fn (Get $get): bool => 'customer' === $get('sender_type'))
                ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                    if ('customer' === $get('sender_type')) {
                        $customerId = $get('customer_id');
                        if ($customerId && $state) {
                            $user = User::find($customerId);
                            $address = Address::find($state);
                            if ($user && $address) {
                                $set('sender_street', $address->street);
                                $set('sender_zip_code', $address->zip_code);
                                $set('sender_city_id', $address->city?->id);
                                $set('sender_region_id', $address->region?->id);
                                $set('sender_country_id', $address->country?->id);

                            }
                        }
                    }
                }),
        ];
    }
}
