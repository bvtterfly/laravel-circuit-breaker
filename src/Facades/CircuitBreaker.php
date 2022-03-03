<?php

namespace Bvtterfly\LaravelCircuitBreaker\Facades;

use Bvtterfly\LaravelCircuitBreaker\CircuitBreakerManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Bvtterfly\LaravelCircuitBreaker\CircuitBreakerManager
* @method static \Bvtterfly\LaravelCircuitBreaker\CircuitBreaker service(string $service, array $config) Retrieve a circuit breaker service from the cache by service name.
 */
class CircuitBreaker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CircuitBreakerManager::class;
    }
}
