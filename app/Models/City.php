<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\CityPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(CityPolicy::class)]
class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar', 'name_en', 'name_ur', 'is_active', 'region_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    protected function casts(): array
    {
        return [

        ];
    }
}
