<?php

namespace JeffersonGoncalves\Gtag\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\Gtag\Settings\GtagSettings;

/**
 * @property ?string $gtag_id
 * @property bool $enabled
 * @property bool $anonymize_ip
 * @property array $additional_config
 *
 * @see \JeffersonGoncalves\Gtag\Settings\GtagSettings
 */
class Gtag extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GtagSettings::class;
    }
}
