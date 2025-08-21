<?php

declare(strict_types=1);

namespace App\Filament\Resources\PricingRules\Pages;

use App\Filament\Resources\PricingRules\PricingRuleResource;
use App\Models\PricingRule;
use Filament\Notifications\Notification;
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $intervals = collect($data['pricing_table'] ?? [])->sortBy('weight_from')->values();

        foreach ($intervals as $i => $interval) {
            if ($interval['weight_from'] >= $interval['weight_to']) {
                Notification::make()
                    ->title(__('Validation Error'))
                    ->body(__('Interval :number: Weight From (:from kg) must be less than Weight To (:to kg)', [
                        'number' => $i + 1,
                        'from' => $interval['weight_from'],
                        'to' => $interval['weight_to'],
                    ]))
                    ->danger()
                    ->send();

                $this->halt(true);
            }

            $nextInterval = $intervals->get($i + 1);
            if ($nextInterval && $interval['weight_to'] > $nextInterval['weight_from']) {
                Notification::make()
                    ->title(__('Validation Error'))
                    ->body(__('Interval :current overlaps with interval :next. Range :currentRange conflicts with :nextRange', [
                        'current' => $i + 1,
                        'next' => $i + 2,
                        'currentRange' => $interval['weight_from'] . '-' . $interval['weight_to'] . ' kg',
                        'nextRange' => $nextInterval['weight_from'] . '-' . $nextInterval['weight_to'] . ' kg',
                    ]))
                    ->danger()
                    ->send();

                $this->halt(true);
            }
        }

        return $data;
    }
}
