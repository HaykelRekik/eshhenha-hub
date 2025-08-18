<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Settings\ContactChannelsSettings;
use App\Settings\GeneralSettings;
use App\Settings\RewardSettings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->generalSettings();
        $this->socialMediaLinks();
        $this->contactChannels();
        $this->rewardSettings();
    }

    private function generalSettings(): void
    {
        $generalSettings = new GeneralSettings();
        $generalSettings->brand_name = 'Eshhanha';
        $generalSettings->seo_title = 'Eshhanha - Shipping Platform';
        $generalSettings->seo_description = 'Eshhanha is a shipping platform.';
        $generalSettings->save();
    }

    private function socialMediaLinks(): void
    {
        $contactChannelsSettings = app(ContactChannelsSettings::class);
        $contactChannelsSettings->social_media = [
            'facebook' => 'https://facebook.com',
            'x' => 'https://x.com',
            'snapchat' => 'https://snapchat.com',
            'instagram' => 'https://instagram.com',
            'youtube' => 'https://youtube.com',
        ];
        $contactChannelsSettings->save();
    }

    private function contactChannels(): void
    {
        $contactChannelsSettings = app(ContactChannelsSettings::class);
        $contactChannelsSettings->contacts = [
            'email' => 'contact@eshhanha.com',
            'whatsapp_number' => '966501231234',
            'phone_number' => '+966501231234',
            'second_phone_number' => '+966501231234',
        ];
        $contactChannelsSettings->save();
    }

    private function rewardSettings(): void
    {
        $rewardSettings = app(RewardSettings::class);
        $rewardSettings->referrer_user_points = 0;
        $rewardSettings->referred_user_points = 0;
        $rewardSettings->welcome_bonus = 0;
        $rewardSettings->save();
    }
}
