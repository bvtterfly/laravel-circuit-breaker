🚨 THIS PACKAGE HAS BEEN ABANDONED 🚨

I no longer use Laravel and cannot justify the time needed to maintain this package. That's why I have chosen to abandon it. Feel free to fork my code and maintain your own copy.

# Laravel Circuit Breaker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bvtterfly/laravel-circuit-breaker.svg?style=flat-square)](https://packagist.org/packages/bvtterfly/laravel-circuit-breaker) [![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bvtterfly/laravel-circuit-breaker/run-tests?label=tests)](https://github.com/bvtterfly/laravel-circuit-breaker/actions?query=workflow%3Arun-tests+branch%3Amain) [![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bvtterfly/laravel-circuit-breaker/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bvtterfly/laravel-circuit-breaker/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain) [![Total Downloads](https://img.shields.io/packagist/dt/bvtterfly/laravel-circuit-breaker.svg?style=flat-square)](https://packagist.org/packages/bvtterfly/laravel-circuit-breaker)

This package is a simple implementation of circuit breaker pattern for laravel. It protects your application from failures of its service dependencies.

Resources about the circuit breaker pattern:
* [http://martinfowler.com/bliki/CircuitBreaker.html](http://martinfowler.com/bliki/CircuitBreaker.html)
* [https://github.com/Netflix/Hystrix/wiki/How-it-Works#CircuitBreaker](https://github.com/Netflix/Hystrix/wiki/How-it-Works#CircuitBreaker)

## Installation

You can install the package via composer:

```bash
composer require bvtterfly/laravel-circuit-breaker
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="circuit-breaker-config"
```

This is the contents of the published config file:

```php
return [
    // Here you may specify which of your cache stores you wish to use as your default store.
    'store' => config('cache.default'),

    // length of interval (in seconds) over which it calculates the error rate
    'time_window' => 60,

    // the number of errors to encounter within a given timespan before opening the circuit
    'error_threshold' => 10,

    // the amount of time until the circuit breaker will try to query the resource again
    'error_timeout' => 300,

    // the timeout for the circuit when it is in the half-open state
    'half_open_timeout' => 150,

    // the amount of consecutive successes for the circuit to close again
    'success_threshold' => 1,
];
```

## Usage

Your application may have multiple services, so you will have to get a circuit breaker for each service:
```php
use Bvtterfly\LaravelCircuitBreaker\Facades\CircuitBreaker;
$circuit = CircuitBreaker::service('my-service');
// or you can override default configuration:
$circuit = CircuitBreaker::service('my-service', [
    'time_window' => 120,
    'success_threshold' => 3,
]);

```

#### Three states of circuit breaker

<img src="https://user-images.githubusercontent.com/1885716/53690408-4a7f3d00-3dad-11e9-852c-0e082b7b9636.png" width="500">

You can then determine whether a service is available or not.

```php
// Check circuit status for service
if (! $circuit->isAvailable()) {
    // Service isn't available
}
```
Service is available if it's CLOSED or HALF_OPEN. Then, you should call your service, depending on the response. You can mark it as a success or failure to update the circuit status.

```php
try {
    callAPI();
    $circuit->markSuccess();
} catch (\Exception $e) {
    // If an error occurred, it must be recorded as failed.
    $circuit->markFailed();
}
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

- [ARI](https://github.com/bvtterfly)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
