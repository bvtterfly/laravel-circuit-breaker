<?php

namespace Bvtterfly\LaravelCircuitBreaker;

use Illuminate\Cache\CacheManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCircuitBreakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-circuit-breaker')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->singleton(CircuitBreakerManager::class, function () {
            return new CircuitBreakerManager(app(CacheManager::class),config('circuit-breaker'));
        });
    }
}
