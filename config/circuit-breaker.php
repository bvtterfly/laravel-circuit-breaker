<?php
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
