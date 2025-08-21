<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Pages;

use App\Filament\Resources\PricingRules\PricingRuleResource;
use App\Models\PricingRule;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePricingRule extends CreateRecord
{
    protected static string $resource = PricingRuleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $pricingTable = $data['pricing_table'];
        unset($data['pricing_table']);

        $rows = collect($pricingTable)->map(fn ($row): array => [
            'user_id' => $data['user_id'],
            'company_id' => $data['company_id'],
            'shipping_company_id' => $data['shipping_company_id'],
            'type' => $data['type']->value,
            'weight_from' => $row['weight_from'],
            'weight_to' => $row['weight_to'],
            'local_price_per_kg' => $row['local_price_per_kg'],
            'international_price_per_kg' => $row['international_price_per_kg'],
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        PricingRule::insert($rows);

        $model = static::getModel();

        return new $model();
    }
}
