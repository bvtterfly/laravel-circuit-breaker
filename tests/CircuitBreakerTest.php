<?php

use Bvtterfly\LaravelCircuitBreaker\CircuitBreaker;
use Bvtterfly\LaravelCircuitBreaker\Config;
use Illuminate\Cache\Repository;

it('should available & close', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    expect($circuitBreaker->isClose())->toBeTrue();
    expect($circuitBreaker->isAvailable())->toBeTrue();
});

it('can increment errors', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    expect($circuitBreaker->getErrorsCount())->toBe(2);
});

it('should restart errors after mark success its in close state', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markSuccess();
    expect($circuitBreaker->getErrorsCount())->toBe(0);
});

it('can be opened', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    expect($circuitBreaker->getErrorsCount())->toBe(5);
    expect($circuitBreaker->isAvailable())->toBeFalse();
    expect($circuitBreaker->isOpen())->toBeTrue();
    expect($circuitBreaker->isClose())->toBeFalse();
    expect($circuitBreaker->isHalfOpen())->toBeFalse();
});

it('can be half-opened', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $this->travel(31)->seconds();
    expect($circuitBreaker->getErrorsCount())->toBe(0);
    expect($circuitBreaker->isAvailable())->toBeTrue();
    expect($circuitBreaker->isHalfOpen())->toBeTrue();
    $this->travelBack();
});

it('can open again after the half-open mark failed ', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $this->travel(31)->seconds();
    expect($circuitBreaker->getErrorsCount())->toBe(0);
    expect($circuitBreaker->isAvailable())->toBeTrue();
    expect($circuitBreaker->isHalfOpen())->toBeTrue();
    $circuitBreaker->markFailed();
    expect($circuitBreaker->isAvailable())->toBeFalse();
    expect($circuitBreaker->isHalfOpen())->toBeFalse();
    expect($circuitBreaker->isOpen())->toBeTrue();
    $this->travelBack();
});

it('can close after half-open mark success', function () {
    $config = getConfig();
    $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $circuitBreaker->markFailed();
    $this->travel(31)->seconds();
    expect($circuitBreaker->getErrorsCount())->toBe(0);
    expect($circuitBreaker->isAvailable())->toBeTrue();
    expect($circuitBreaker->isHalfOpen())->toBeTrue();
    $circuitBreaker->markSuccess();
    expect($circuitBreaker->isAvailable())->toBeTrue();
    expect($circuitBreaker->isHalfOpen())->toBeTrue();
    expect($circuitBreaker->isOpen())->toBeFalse();
    $circuitBreaker->markSuccess();
    $circuitBreaker->markSuccess();
    expect($circuitBreaker->isAvailable())->toBeTrue();
    expect($circuitBreaker->isClose())->toBeTrue();
    expect($circuitBreaker->isHalfOpen())->toBeFalse();
    expect($circuitBreaker->isOpen())->toBeFalse();
    $this->travelBack();
});


function getConfig()
{
    return Config::fromArray(getDefaultConfig());
}


function getDefaultConfig(): array
{
    return [
        'store' => config('cache.default'),
        'time_window' => 6,
        'error_threshold' => 5,
        'error_timeout' => 30,
        'half_open_timeout' => 15,
        'success_threshold' => 3,
    ];
}
