<?php

use Bvtterfly\LaravelCircuitBreaker\CircuitBreaker;
use Bvtterfly\LaravelCircuitBreaker\Facades\CircuitBreaker as CircuitBreakerFacade;

it('can create a circuit breaker', function () {
    expect(CircuitBreakerFacade::service('test', []))
        ->toBeInstanceOf(CircuitBreaker::class)
    ;
});

it('should cache a circuit breaker', function () {
    $circuit1 = CircuitBreakerFacade::service('test', []);
    $circuit2 = CircuitBreakerFacade::service('test', []);

    expect($circuit1)->toEqual($circuit2);
});
