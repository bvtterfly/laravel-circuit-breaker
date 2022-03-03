<?php

namespace Bvtterfly\LaravelCircuitBreaker;

class Config
{
    public const KEYS = [
        'store',
        'time_window',
        'error_threshold',
        'error_timeout',
        'half_open_timeout',
        'success_threshold',
    ];

    public function __construct(
        public $store,
        public $timeWindow,
        public $errorThreshold,
        public $errorTimeout,
        public $halfOpenTimeout,
        public $successThreshold
    )
    {
    }

    public static function fromArray(array $config)
    {
        return new self(
            data_get($config, 'store'),
            data_get($config, 'time_window'),
            data_get($config, 'error_threshold'),
            data_get($config, 'error_timeout'),
            data_get($config, 'half_open_timeout'),
            data_get($config, 'success_threshold'),
        );
    }
}
