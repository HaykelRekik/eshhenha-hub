<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PricingRuleType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

class PricingRule extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'shipping_company_id',
        'weight_from',
        'weight_to',
        'local_price_per_kg',
        'international_price_per_kg',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    #[Scope]
    public function forCustomers($query, int $customerId)
    {
        return $query->where('user_id', $customerId);
    }

    #[Scope]
    public function forShippingCompany(Builder $query, int $shippingCompanyId): Builder
    {
        return $query->where('shipping_company_id', $shippingCompanyId);
    }

    #[Scope]
    public function forCompany(Builder $query, int $companyID): Builder
    {
        return $query->where('company_id', $companyID);
    }

    #[Scope]
    public function forWeight(Builder $query, float $weight): Builder
    {
        return $query->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight);
    }

    #[Scope]
    public function ofType(Builder $query, PricingRuleType $type): Builder
    {
        return $query->where('type', $type);
    }

    protected function casts(): array
    {
        return [
            'type' => PricingRuleType::class,
        ];
    }
}
