<?php

namespace JeffersonGoncalves\Gtag\Settings;

use Spatie\LaravelSettings\Settings;

class GtagSettings extends Settings
{
    public ?string $gtag_id;

    public bool $enabled;

    public bool $anonymize_ip;

    public array $additional_config;

    public static function group(): string
    {
        return 'gtag';
    }
}
