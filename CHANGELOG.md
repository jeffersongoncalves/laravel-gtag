# Changelog

All notable changes to this project will be documented in this file.

## v2.0.0 - 2026-02-20

### Breaking Changes

- **Config file removed**: `config/gtag.php` has been replaced with database-backed settings via [spatie/laravel-settings](https://github.com/spatie/laravel-settings)
- **Environment variable removed**: `GTAG_ID` is no longer supported

### What's New

- **Database settings**: All configuration is now stored in the database, enabling dynamic management via code or admin panels
- **New properties**: `enabled`, `anonymize_ip`, `additional_config` for fine-grained control
- **Helper function**: `gtag_settings()` for quick access
- **Facade**: `Gtag` facade with full PHPDoc support
- **GDPR support**: Built-in `anonymize_ip` option
- **Tests**: 10 Pest tests with full coverage

### Upgrade Guide

1. Remove the old config file: `config/gtag.php`
2. Remove the `GTAG_ID` environment variable
3. Publish and run the settings migration:
   ```bash
   php artisan vendor:publish --tag=gtag-settings-migrations
   php artisan migrate

   ```
4. Set your Google Tag ID via code:
   ```php
   gtag_settings()->gtag_id = 'G-XXXXXXXXXX';
   gtag_settings()->save();

   ```

## 1.0.0 - 2025-05-01

**Full Changelog**: https://github.com/jeffersongoncalves/laravel-gtag/commits/1.0.0
