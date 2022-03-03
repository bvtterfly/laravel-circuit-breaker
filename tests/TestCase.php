<?php

namespace Bvtterfly\LaravelCircuitBreaker\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Bvtterfly\LaravelCircuitBreaker\LaravelCircuitBreakerServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCircuitBreakerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
//        config()->set('database.default', 'testing');

    }
}
