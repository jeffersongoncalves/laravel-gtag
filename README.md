<div class="filament-hidden">

![Laravel Google Analytics](https://raw.githubusercontent.com/jeffersongoncalves/laravel-gtag/master/art/jeffersongoncalves-laravel-gtag.png)

</div>

# Laravel Google Analytics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-gtag.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-gtag)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-gtag/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-gtag/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-gtag.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-gtag)

This Laravel package provides a straightforward integration of Google Analytics using Gtag into your Blade templates. It enables you to easily track website visits and user engagement, offering valuable insights into your site's performance. With minimal setup, you can leverage Gtag's powerful analytics features to better understand your audience and improve your website's effectiveness.

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-gtag
```

## Usage

Publish config file.

```bash
php artisan vendor:publish --tag=gtag-config
```

Add head template.

```php
@include('gtag::script')
```

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
