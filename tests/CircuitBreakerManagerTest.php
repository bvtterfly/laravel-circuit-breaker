<?php

use Bvtterfly\LaravelCircuitBreaker\CircuitBreaker;
use Bvtterfly\LaravelCircuitBreaker\CircuitBreakerManager;

it('can create a circuit breaker', function () {
    $breaker = app(CircuitBreakerManager::class);
    expect($breaker)
        ->service('test', [])
        ->toBeInstanceOf(CircuitBreaker::class)
    ;
});

it('should cache a circuit breaker', function () {
    /** @var CircuitBreakerManager $breaker */
    $breaker = app(CircuitBreakerManager::class);
    $circuit1 = $breaker->service('test', []);
    $circuit2 = $breaker->service('test', []);

    expect($circuit1)->toEqual($circuit2);
});
