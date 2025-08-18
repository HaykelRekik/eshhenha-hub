<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasAddresses, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'logo',
        'is_active',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //    public function warehouses(): HasMany
    //    {
    //        return $this->hasMany(Warehouse::class, 'company_id');
    //    }

    //    public function contracts(): BelongsToMany
    //    {
    //        return $this->belongsToMany(ShippingCompany::class, 'contracts')->withTimestamps();
    //    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
