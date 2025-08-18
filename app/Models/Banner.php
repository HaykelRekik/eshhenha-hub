<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\BannerObserver;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BannerObserver::class)]
class Banner extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'image',
        'title_ar',
        'title_en',
        'title_ur',
        'description_ar',
        'description_en',
        'description_ur',
        'link',
        'is_active',
        'position',
        'clicks_count',
    ];

    protected array $translatable = [
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
