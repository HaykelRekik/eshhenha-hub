<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use App\Models\Address;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //        if (null !== $data['recipient_address_id']) {
        //            $recipientAddress = Address::find($data['recipient_address_id']);
        //            $data['recipient_name'] = $recipientAddress->contact_name;
        //            $data['recipient_phone'] = $recipientAddress->contact_phone_number;
        //            $data['recipient_street'] = $recipientAddress->street;
        //            $data['recipient_city'] = $recipientAddress->city?->{'name_' . app()->getLocale()};
        //            $data['recipient_region'] = $recipientAddress->region?->{'name_' . app()->getLocale()};
        //            $data['recipient_country'] = $recipientAddress->country?->{'name_' . app()->getLocale()};
        //            $data['recipient_zip'] = $recipientAddress->zip_code;
        //        }
        dd($data);

        return $data;
    }
}
