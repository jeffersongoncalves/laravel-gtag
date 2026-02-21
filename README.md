<div class="filament-hidden">

![Laravel Google Analytics](https://raw.githubusercontent.com/jeffersongoncalves/laravel-gtag/master/art/jeffersongoncalves-laravel-gtag.png)

</div>

# Laravel Google Analytics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-gtag.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-gtag)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-gtag/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-gtag/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-gtag.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-gtag)

This Laravel package provides a straightforward integration of Google Analytics using Gtag into your Blade templates. It enables you to easily track website visits and user engagement, offering valuable insights into your site's performance. With minimal setup, you can leverage Gtag's powerful analytics features to better understand your audience and improve your website's effectiveness.

Settings are stored in the database using [spatie/laravel-settings](https://github.com/spatie/laravel-settings), allowing dynamic management via admin panels or code.

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-gtag
```

Publish and run the settings migration:

```bash
php artisan vendor:publish --tag=gtag-settings-migrations
php artisan migrate
```

## Usage

Add the script tag to your Blade layout (typically in the `<head>`):

```php
@include('gtag::script')
```

### Configuration via Code

You can update settings at any time using the `GtagSettings` class, the `gtag_settings()` helper, or the `Gtag` facade:

```php
use JeffersonGoncalves\Gtag\Settings\GtagSettings;

// Via container
$settings = app(GtagSettings::class);
$settings->gtag_id = 'G-XXXXXXXXXX';
$settings->enabled = true;
$settings->save();

// Via helper
gtag_settings()->gtag_id = 'G-XXXXXXXXXX';
gtag_settings()->save();

// Via Facade
use JeffersonGoncalves\Gtag\Facades\Gtag;

$gtag = Gtag::getFacadeRoot();
$gtag->gtag_id = 'G-XXXXXXXXXX';
$gtag->save();
```

### Available Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `gtag_id` | `?string` | `null` | Google Tag ID (e.g., `G-XXXXXXXXXX`) |
| `enabled` | `bool` | `true` | Enable/disable script rendering |
| `anonymize_ip` | `bool` | `false` | Anonymize visitor IPs (GDPR compliance) |
| `additional_config` | `array` | `[]` | Extra parameters for `gtag('config', ...)` |

### Additional Config Example

```php
$settings = gtag_settings();
$settings->additional_config = [
    'send_page_view' => false,
    'cookie_domain' => 'example.com',
];
$settings->save();
```

## Upgrading from v1

Version 2 replaces the `config/gtag.php` configuration file with database settings via `spatie/laravel-settings`. This is a **breaking change**.

1. Remove the old config file: `config/gtag.php`
2. Remove the `GTAG_ID` environment variable (no longer used)
3. Publish and run the settings migration
4. Set your Google Tag ID via code (see Usage above)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jèfferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
