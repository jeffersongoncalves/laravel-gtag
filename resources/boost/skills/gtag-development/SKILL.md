---
name: gtag-development
description: Development skill for the laravel-gtag package -- Google Analytics (gtag.js) integration for Laravel using spatie/laravel-settings
---

## When to use this skill

- Adding or modifying Google Analytics tracking behavior
- Working with the `GtagSettings` class or its properties
- Updating the Blade script view (`gtag::script`)
- Writing tests for the laravel-gtag package
- Adding new gtag.js configuration options or tracking features

## Setup

### Requirements
- PHP 8.2+
- Laravel 11, 12, or 13
- `spatie/laravel-settings` ^3.0
- `spatie/laravel-package-tools` ^1.14.0

### Installation
```bash
composer require jeffersongoncalves/laravel-gtag
php artisan vendor:publish --tag=gtag-settings-migrations
php artisan migrate
```

### Package structure
```
laravel-gtag/
├── src/
│   ├── GtagServiceProvider.php      # Package service provider
│   ├── Facades/Gtag.php            # Facade for GtagSettings
│   ├── Settings/GtagSettings.php    # Settings class (spatie/laravel-settings)
│   └── helpers.php                  # gtag_settings() helper function
├── database/
│   └── settings/
│       └── 2026_02_20_000000_create_gtag_settings.php
├── resources/
│   └── views/
│       └── script.blade.php         # Google Analytics tracking script template
└── tests/
    ├── TestCase.php                 # Base test case with SQLite setup
    ├── Pest.php                     # Pest configuration
    └── GtagSettingsTest.php         # Settings and view rendering tests
```

## Features

### GtagSettings class

The core settings class extends `Spatie\LaravelSettings\Settings` with group `gtag`:

```php
namespace JeffersonGoncalves\Gtag\Settings;

use Spatie\LaravelSettings\Settings;

class GtagSettings extends Settings
{
    public ?string $gtag_id;        // Google measurement ID (e.g., 'G-XXXXXXXXXX')
    public bool $enabled;            // Enable/disable tracking (default: true)
    public bool $anonymize_ip;       // Anonymize visitor IP addresses (default: false)
    public array $additional_config;  // Extra gtag config parameters (default: [])

    public static function group(): string
    {
        return 'gtag';
    }
}
```

### Settings defaults (from migration)

| Property            | Default | Description                                |
|---------------------|---------|--------------------------------------------|
| `gtag_id`           | `null`  | Google Analytics measurement ID            |
| `enabled`           | `true`  | Whether tracking is active                 |
| `anonymize_ip`      | `false` | Anonymize visitor IP addresses             |
| `additional_config` | `[]`    | Extra key-value pairs for gtag config call |

### Accessing settings

```php
// Helper function (globally available)
$settings = gtag_settings();

// Via Laravel container
$settings = app(\JeffersonGoncalves\Gtag\Settings\GtagSettings::class);

// Via Facade
use JeffersonGoncalves\Gtag\Facades\Gtag;
$root = Gtag::getFacadeRoot(); // returns GtagSettings instance

// Update and persist
$settings->gtag_id = 'G-XXXXXXXXXX';
$settings->enabled = true;
$settings->anonymize_ip = true;
$settings->additional_config = [
    'send_page_view' => false,
    'cookie_domain' => 'example.com',
];
$settings->save();
```

### Blade view rendering

Include in your layout's `<head>`:

```blade
@include('gtag::script')
```

The view renders the standard Google Analytics gtag.js snippet:

```html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-XXXXXXXXXX', {"anonymize_ip":true,"send_page_view":false});
</script>
```

Rendering rules:
- Script only renders when `enabled` is `true` AND `gtag_id` is not empty
- When `anonymize_ip` is `true`, `{"anonymize_ip": true}` is added to config params
- `additional_config` array is merged into config params (supports any gtag.js option)
- When config params are empty, `gtag('config', 'ID')` renders without a third argument
- When config params exist, they are JSON-encoded with `JSON_UNESCAPED_SLASHES`

### Service provider

`GtagServiceProvider` extends `PackageServiceProvider` from spatie/laravel-package-tools:

```php
// Registers the package with views
$package->name('laravel-gtag')->hasViews();

// Registers GtagSettings in spatie/laravel-settings config
$settings = Config::get('settings.settings', []);
$settings[] = GtagSettings::class;
Config::set('settings.settings', $settings);

// Publishes settings migrations with tag 'gtag-settings-migrations'
$this->publishes([
    $migrationPath => database_path('settings'),
], 'gtag-settings-migrations');

// Also loads migrations directly
$this->loadMigrationsFrom($migrationPath);
```

### Facade

The `Gtag` facade resolves to `GtagSettings::class` and is auto-aliased:

```php
use JeffersonGoncalves\Gtag\Facades\Gtag;

// Documented properties on the facade
// @property ?string $gtag_id
// @property bool $enabled
// @property bool $anonymize_ip
// @property array $additional_config

Gtag::getFacadeRoot(); // returns GtagSettings instance
```

## Configuration

This package uses **no config files**. All configuration is stored in the database via `spatie/laravel-settings`.

The settings migration creates entries in the `settings` table under the `gtag` group. Publish and run:

```bash
php artisan vendor:publish --tag=gtag-settings-migrations
php artisan migrate
```

### Additional config examples

The `additional_config` array supports any valid gtag.js configuration parameter:

```php
$settings = gtag_settings();
$settings->additional_config = [
    'send_page_view' => false,       // Disable automatic page view
    'cookie_domain' => 'example.com', // Set cookie domain
    'cookie_flags' => 'SameSite=None;Secure',
    'linker' => ['domains' => ['example.com', 'app.example.com']],
];
$settings->save();
```

## Testing patterns

Tests use **Pest** with **Orchestra Testbench**. The base `TestCase` sets up:
- SQLite in-memory database
- `settings` table schema (id, group, name, locked, payload, timestamps)
- Default seed values inserted directly via DB facade

### Settings test pattern

```php
use JeffersonGoncalves\Gtag\Settings\GtagSettings;

it('resolves GtagSettings from the container', function () {
    $settings = app(GtagSettings::class);
    expect($settings)->toBeInstanceOf(GtagSettings::class);
});

it('has correct default values', function () {
    $settings = app(GtagSettings::class);
    expect($settings->gtag_id)->toBeNull()
        ->and($settings->enabled)->toBeTrue()
        ->and($settings->anonymize_ip)->toBeFalse()
        ->and($settings->additional_config)->toBe([]);
});

it('persists updated settings', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->save();

    $fresh = app(GtagSettings::class);
    expect($fresh->gtag_id)->toBe('G-TESTID123');
});
```

### Blade view test pattern

```php
it('renders script when enabled with valid gtag_id', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = true;
    $settings->save();

    $view = view('gtag::script')->render();
    expect($view)
        ->toContain('googletagmanager.com/gtag/js?id=G-TESTID123')
        ->toContain("gtag('config', 'G-TESTID123')");
});

it('does not render script when disabled', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = false;
    $settings->save();

    $view = view('gtag::script')->render();
    expect($view)->not->toContain('googletagmanager.com');
});

it('renders anonymize_ip when enabled', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = true;
    $settings->anonymize_ip = true;
    $settings->save();

    $view = view('gtag::script')->render();
    expect($view)->toContain('"anonymize_ip":true');
});

it('renders additional_config parameters', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = true;
    $settings->additional_config = ['send_page_view' => false];
    $settings->save();

    $view = view('gtag::script')->render();
    expect($view)->toContain('"send_page_view":false');
});
```

### Running tests

```bash
# Run all tests
vendor/bin/pest

# Run with coverage
vendor/bin/pest --coverage

# Run static analysis
vendor/bin/phpstan analyse

# Format code
vendor/bin/pint
```

### TestCase setup reference

The gtag TestCase seeds settings directly via DB instead of using `SettingsMigrator`:

```php
namespace JeffersonGoncalves\Gtag\Tests;

use JeffersonGoncalves\Gtag\GtagServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelSettings\LaravelSettingsServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelSettingsServiceProvider::class,
            GtagServiceProvider::class,
        ];
    }

    protected function seedDefaultSettings(): void
    {
        $defaults = [
            ['group' => 'gtag', 'name' => 'gtag_id', 'payload' => json_encode(null)],
            ['group' => 'gtag', 'name' => 'enabled', 'payload' => json_encode(true)],
            ['group' => 'gtag', 'name' => 'anonymize_ip', 'payload' => json_encode(false)],
            ['group' => 'gtag', 'name' => 'additional_config', 'payload' => json_encode([])],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->insert(
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
```
