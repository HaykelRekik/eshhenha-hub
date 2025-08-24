<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasTranslations;

    protected array $translatable = ['name', 'description'];

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'supplier_name',
        'external_identifier',
        'quantity',
        'required_points',
        'is_active',
    ];

    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class, 'reward_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
