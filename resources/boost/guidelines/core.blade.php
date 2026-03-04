## Laravel Gtag

### Overview
Laravel package that integrates Google Analytics (gtag.js) into Blade templates using `spatie/laravel-settings` for database-stored configuration. No config files -- all settings are managed via the `GtagSettings` class and stored in the `settings` database table.

### Key Concepts
- **Settings-driven**: All configuration lives in `GtagSettings` (group: `gtag`), not in config files
- **Blade view**: Include `gtag::script` in your layout to render the Google Analytics script
- **Facade + Helper**: Access settings via `Gtag` facade or `gtag_settings()` helper
- **Auto-discovery**: Service provider and facade alias are auto-discovered via `composer.json`

### Settings (spatie/laravel-settings)

@verbatim
<code-snippet name="gtag-settings-class" lang="php">
use Spatie\LaravelSettings\Settings;

class GtagSettings extends Settings
{
    public ?string $gtag_id;       // Google Analytics measurement ID (e.g., 'G-XXXXXXXXXX')
    public bool $enabled;           // Enable/disable tracking (default: true)
    public bool $anonymize_ip;      // Anonymize visitor IP (default: false)
    public array $additional_config; // Extra gtag config params (default: [])

    public static function group(): string
    {
        return 'gtag';
    }
}
</code-snippet>
@endverbatim

### Configuration

Settings migration path: `database/settings/2026_02_20_000000_create_gtag_settings.php`

Publish migrations:
@verbatim
<code-snippet name="publish-migrations" lang="bash">
php artisan vendor:publish --tag=gtag-settings-migrations
</code-snippet>
@endverbatim

### Usage

Include the tracking script in your Blade layout (typically before `</head>`):
@verbatim
<code-snippet name="blade-include" lang="blade">
@include('gtag::script')
</code-snippet>
@endverbatim

Access settings programmatically:
@verbatim
<code-snippet name="access-settings" lang="php">
// Via helper
$settings = gtag_settings();
$settings->gtag_id = 'G-XXXXXXXXXX';
$settings->enabled = true;
$settings->anonymize_ip = true;
$settings->additional_config = ['send_page_view' => false, 'cookie_domain' => 'example.com'];
$settings->save();

// Via Facade
use JeffersonGoncalves\Gtag\Facades\Gtag;
$gtagId = app(Gtag::getFacadeAccessor())->gtag_id;

// Via container
$settings = app(\JeffersonGoncalves\Gtag\Settings\GtagSettings::class);
</code-snippet>
@endverbatim

### Conventions
- Namespace: `JeffersonGoncalves\Gtag`
- Service provider: `GtagServiceProvider` extends `PackageServiceProvider` (spatie/laravel-package-tools)
- Settings group name: `gtag`
- View namespace: `gtag` (e.g., `gtag::script`)
- The script only renders when both `enabled` is `true` AND `gtag_id` is not empty
- `anonymize_ip` is merged into the `gtag('config', ...)` call when `true`
- `additional_config` array is merged into config params (supports any gtag.js config option)
- Config params are JSON-encoded with `JSON_UNESCAPED_SLASHES`
- Tests use Pest with Orchestra Testbench and SQLite in-memory database
