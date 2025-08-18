<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $brand_name;

    public string $seo_title;

    public string $seo_description;

    public static function group(): string
    {
        return 'general';
    }
}
