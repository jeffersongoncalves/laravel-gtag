<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('gtag.gtag_id', null);
        $this->migrator->add('gtag.enabled', true);
        $this->migrator->add('gtag.anonymize_ip', false);
        $this->migrator->add('gtag.additional_config', []);
    }
};
