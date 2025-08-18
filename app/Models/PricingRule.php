<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    protected $fillable = [
        'company_id',
        'shipping_company_id',
        'weight_from',
        'weight_to',
        'local_price_per_kg',
        'international_price_per_kg',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    #[Scope]
    public function forCustomers($query)
    {
        return $query->whereNull('company_id');
    }

    #[Scope]
    public function forShippingCompany($query, int $shippingCompanyId)
    {
        return $query->where('shipping_company_id', $shippingCompanyId);
    }

    #[Scope]
    public function forCompany($query, int $companyID)
    {
        return $query->where('company_id', $companyID);
    }

    #[Scope]
    public function forForWeight($query, float $weight)
    {
        return $query->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight);
    }
}
