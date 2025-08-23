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
        $data = collect($data);

        $pricingTable = $data->get('pricing_table', []);
        $rows = collect($pricingTable)->map(fn ($row): array => [
            'user_id' => $data->get('user_id'),
            'company_id' => $data->get('company_id'),
            'shipping_company_id' => $data->get('shipping_company_id'),
            'type' => optional($data->get('type'))->value,
            'weight_from' => $row['weight_from'] ?? null,
            'weight_to' => $row['weight_to'] ?? null,
            'local_price_per_kg' => $row['local_price_per_kg'] ?? null,
            'international_price_per_kg' => $row['international_price_per_kg'] ?? null,
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
                    ->body(__('Interval :number: Weight From (:from ' . __('KG') . ') must be less than Weight To (:to ' . __('KG') . ')', [
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
                        'currentRange' => $interval['weight_from'] . '-' . $interval['weight_to'] . ' ' . __('KG'),
                        'nextRange' => $nextInterval['weight_from'] . '-' . $nextInterval['weight_to'] . ' ' . __('KG'),
                    ]))
                    ->danger()
                    ->send();

                $this->halt(true);
            }

            $exists = PricingRule::query()
                ->where('type', $data['type'])
                ->when($data['user_id'] ?? null, fn ($q, $userId) => $q->where('user_id', $userId))
                ->when($data['company_id'] ?? null, fn ($q, $companyId) => $q->where('company_id', $companyId))
                ->when($data['shipping_company_id'] ?? null, fn ($q, $shipId) => $q->where('shipping_company_id', $shipId))
                ->where(function ($q) use ($interval): void {
                    $q->where('weight_from', '<=', $interval['weight_to'])
                        ->where('weight_to', '>=', $interval['weight_from']);
                })
                ->exists();
            if ($exists) {
                Notification::make()
                    ->title(__('Validation Error'))
                    ->body(__('Range :range conflicts with an existing pricing rule.', [
                        'range' => $interval['weight_from'] . '-' . $interval['weight_to'] . ' ' . __('KG'),

                    ]))
                    ->danger()
                    ->send();

                $this->halt(true);
            }

        }

        return $data;
    }
}
