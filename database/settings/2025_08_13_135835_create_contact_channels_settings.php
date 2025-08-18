<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('contact_channels.social_media', [
            'facebook' => 'https://facebook.com',
            'x' => 'https://x.com',
            'snapchat' => 'https://snapchat.com',
            'instagram' => 'https://instagram.com',
            'youtube' => 'https://youtube.com',
        ]);
        $this->migrator->add('contact_channels.contacts', [
            'email' => 'contact@eshhanha.com',
            'whatsapp_number' => '966501231234',
            'phone_number' => '+966501231234',
            'second_phone_number' => '+966501231234',
        ]);
    }
};
