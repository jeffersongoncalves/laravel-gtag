<?php

namespace JeffersonGoncalves\Gtag;

use Illuminate\Support\Facades\Config;
use JeffersonGoncalves\Gtag\Settings\GtagSettings;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GtagServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-gtag')
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        /** @var array<int, class-string> $settings */
        $settings = Config::get('settings.settings', []);
        $settings[] = GtagSettings::class;
        Config::set('settings.settings', $settings);
    }

    public function packageBooted(): void
    {
        $migrationPath = __DIR__.'/../database/settings';

        $this->publishes([
            $migrationPath => database_path('settings'),
        ], 'gtag-settings-migrations');

        $this->loadMigrationsFrom($migrationPath);
    }
}
