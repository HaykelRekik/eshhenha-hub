<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas;

use App\Filament\Resources\Shipments\Schemas\Steps\RecipientInformation\RecipientInformationStep;
use App\Filament\Resources\Shipments\Schemas\Steps\SenderInformation\SenderInformationStep;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // Step 1: Sender Information
                    SenderInformationStep::make(),

                    // Step 2 Recipient Information
                    RecipientInformationStep::make(),
                ])->skippable(false),
            ]);
    }
}
