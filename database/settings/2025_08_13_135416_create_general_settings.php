<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.brand_name', 'Eshhanha');
        $this->migrator->add('general.seo_title', 'Eshhanha - Shipping Platform');
        $this->migrator->add('general.seo_description', 'Eshhanha is a shipping platform.');
    }
};
