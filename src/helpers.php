<?php

use JeffersonGoncalves\Gtag\Settings\GtagSettings;

if (! function_exists('gtag_settings')) {
    function gtag_settings(): GtagSettings
    {
        return app(GtagSettings::class);
    }
}
