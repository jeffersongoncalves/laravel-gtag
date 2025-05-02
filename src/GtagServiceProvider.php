<?php

namespace JeffersonGoncalves\Gtag;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GtagServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-gtag')
            ->hasConfigFile('gtag')
            ->hasViews();
    }
}
