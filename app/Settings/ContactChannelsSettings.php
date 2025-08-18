<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactChannelsSettings extends Settings
{
    public array $social_media;

    public array $contacts;

    public static function group(): string
    {
        return 'contact_channels';
    }
}
