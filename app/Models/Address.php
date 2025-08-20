<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'contact_name',
        'contact_phone_number',
        'is_recipient_address',
        'street',
        'zip_code',
        'is_default',
        'country_id',
        'region_id',
        'city_id',
    ];

    /**
     * The relationships that should always be loaded with specific fields only.
     * This optimizes the query by selecting only needed fields.
     *
     * @var array
     */
    protected $with = [
        'country:id,name_ar,name_en',
        'region:id,name_ar,name_en',
        'city:id,name_ar,name_en',
    ];

    protected $appends = ['full_address'];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $parts = [
                    $this->street,
                    $this->city?->{'name_' . app()->getLocale() ?? 'name_ar'},
                    $this->region?->{'name_' . app()->getLocale() ?? 'name_ar'},
                    $this->zip_code,
                    $this->country?->{'name_' . app()->getLocale() ?? 'name_ar'},
                ];

                return implode(', ', array_filter($parts));
            }
        );
    }

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }
}
