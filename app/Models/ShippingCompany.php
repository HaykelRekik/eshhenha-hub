<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShippingCompanyInsuranceType;
use App\Enums\ShippingRange;
use App\Traits\HasActiveScope;
use App\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ShippingCompany extends Model
{
    use HasActiveScope, HasAddresses, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'logo',
        'phone_number',
        'is_active',
        'insurance_type',
        'insurance_value',
        'bank_code',
        'bank_account_number',
        'iban',
        'swift',
        'shipping_range',
        'home_pickup_cost',
    ];

    public function deliveryZones(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Region::class,
            table: 'shipping_company_delivery_zones'
        )->withTimestamps();
    }

    #[Scope]
    public function OperatesInRegion(Builder $query, int $regionId): Builder
    {
        return $query->whereRelation(relation: 'deliveryZones', column: 'region_id', operator: '=', value: $regionId);
    }

    public function contracts(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'contracts')->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'insurance_type' => ShippingCompanyInsuranceType::class,
            'shipping_range' => ShippingRange::class,
            'is_active' => 'boolean',
        ];
    }

    protected function hasHomePickup(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => null !== $this->attributes['home_pickup_cost'] && $this->attributes['home_pickup_cost'] > 0,
        );
    }

    protected function insurance(): Attribute
    {
        return Attribute::make(
            get: fn (): HtmlString => new HtmlString(
                html: (ShippingCompanyInsuranceType::PERCENTAGE === $this->insurance_type
                    ? $this->insurance_value . '%'
                    : $this->insurance_value) . view('filament.components.saudi-riyal')
            ),
        );
    }

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->attributes['logo']) {
                    return Storage::disk('public')->url($this->attributes['logo']);
                }

                return 'https://ui-avatars.com/api/?name=' . urlencode((string) $this->attributes['name']) . '&background=0D8ABC&color=fff';
            }
        );
    }
}
