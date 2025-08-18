<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name_ar',
        'name_en',
        'name_ur',
        'iso_code',
        'is_active',
    ];

    protected array $translatable = [
        'name',
    ];

    public function regions(): HasMany
    {
        return $this->hasMany(related: Region::class);
    }

    public function cities(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: City::class,
            through: Region::class,
            firstKey: 'country_id',
            secondKey: 'region_id',
            localKey: 'id',
            secondLocalKey: 'id'
        );
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the county flag path.
     */
    protected function flag(): Attribute
    {
        return Attribute::make(
            get: fn () => asset('flags/' . str($this->attributes['iso_code'])->lower()->value() . '.webp'),
        );
    }
}
