<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShipmentDeliveryStatus;
use App\Enums\ShipmentPaymentMethod;
use App\Enums\ShipmentPaymentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'senderable_type',
        'senderable_id',
        'shipping_company_id',
        'address_id',
        'warehouse_id',
        'recipient_name',
        'recipient_phone',
        'recipient_street',
        'recipient_zip',
        'recipient_city',
        'recipient_region',
        'recipient_country',
        'shipping_date',
        'weight',
        'length',
        'width',
        'height',
        'content_description',
        'delivery_instructions',
        'base_price',
        'insurance_price',
        'home_pickup_price',
        'total_price',
        'payment_status',
        'payment_method',
        'status',
        'raw_response',
        'sender_city_id',
        'sender_region_id',
        'sender_country_id',
    ];

    public function senderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Get the pickup address for the shipment.
     * NOTE: This is intended for when the sender is a User.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the warehouse for the shipment.
     * NOTE: This is only valid when the sender is a Company.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    protected function casts(): array
    {
        return [
            'shipping_date' => 'datetime',

            'weight' => 'decimal:2',
            'length' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'base_price' => 'decimal:2',
            'insurance_price' => 'decimal:2',
            'home_pickup_price' => 'decimal:2',
            'total_price' => 'decimal:2',

            'payment_status' => ShipmentPaymentStatus::class,
            'payment_method' => ShipmentPaymentMethod::class,
            'status' => ShipmentDeliveryStatus::class,

            'raw_response' => 'array',
        ];
    }

    protected function recipient(): Attribute
    {
        return Attribute::make(
            get: fn (): array => [
                'name' => $this->recipient_name,
                'phone' => $this->recipient_phone,
                'street' => $this->recipient_street,
                'city' => $this->recipient_city,
                'region' => $this->recipient_region,
                'zip' => $this->recipient_zip,
                'country' => $this->recipient_country,
            ]
        );
    }
}
