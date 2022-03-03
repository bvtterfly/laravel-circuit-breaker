<?php

namespace Bvtterfly\LaravelCircuitBreaker;

use Illuminate\Contracts\Cache\Repository;

class CircuitBreaker
{
    public const OPEN = 'open';
    public const HALF_OPEN = 'half-open';
    public const ERRORS = 'errors';
    public const SUCCESSES = 'successes';

    public function __construct(
        protected string     $service,
        protected Config     $config,
        protected Repository $cache
    )
    {
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return (bool)$this->cache->get(
            $this->getKeyName(CircuitBreaker::OPEN),
            0
        );
    }

    public function isHalfOpen(): bool
    {
        return ! $this->isOpen() && $this->cache->get(
            $this->getKeyName(CircuitBreaker::HALF_OPEN),
            0
        );
    }

    public function isClose(): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        return ! $this->isHalfOpen();
    }

    /**
     * Set new error for a service
     *
     * @return void
     */
    public function markFailed(): void
    {
        if ($this->isHalfOpen()) {
            $this->openCircuit();

            return;
        }

        $this->incrementErrors();

        if ($this->reachedErrorThreshold() && ! $this->isOpen()) {
            $this->openCircuit();
        }
    }

    /**
     * @return bool
     */
    public function reachedErrorThreshold(): bool
    {
        $failures = $this->getErrorsCount();

        return ($failures >= $this->config->errorThreshold);
    }

    /**
     * @return bool
     */
    public function reachedSuccessThreshold(): bool
    {
        $successes = $this->getSuccessesCount();

        return ($successes >= $this->config->successThreshold);
    }

    /**
     * @return bool
     */
    protected function incrementErrors(): bool
    {
        $key = $this->getKeyName(CircuitBreaker::ERRORS);

        if (! $this->cache->get($key)) {
            return $this->cache->put($key, 1, $this->config->timeWindow);
        }

        return (bool)$this->cache->increment($key);
    }

    /**
     * @return bool
     */
    protected function incrementSuccesses(): bool
    {
        $key = $this->getKeyName(CircuitBreaker::SUCCESSES);

        if (! $this->cache->get($key)) {
            return $this->cache->put($key, 1, $this->config->timeWindow);
        }

        return (bool)$this->cache->increment($key);
    }

    public function markSuccess(): void
    {
        if (! $this->isHalfOpen()) {
            $this->reset();

            return;
        }

        $this->incrementSuccesses();

        if ($this->reachedSuccessThreshold()) {
            $this->reset();
        }
    }

    protected function reset()
    {
        $this->cache->delete($this->getKeyName(CircuitBreaker::OPEN));
        $this->cache->delete($this->getKeyName(CircuitBreaker::SUCCESSES));
        $this->cache->delete($this->getKeyName(CircuitBreaker::HALF_OPEN));
        $this->cache->delete($this->getKeyName(CircuitBreaker::ERRORS));
    }

    protected function setOpenCircuit(): void
    {
        $this->cache->put(
            $this->getKeyName(self::OPEN),
            time(),
            $this->config->errorTimeout
        );
    }

    protected function setHalfOpenCircuit()
    {
        $this->cache->put(
            $this->getKeyName(self::HALF_OPEN),
            time(),
            $this->config->errorTimeout + $this->config->halfOpenTimeout
        );
    }

    public function getErrorsCount(): int
    {
        return (int)$this->cache->get(
            $this->getKeyName(self::ERRORS),
            0
        );
    }

    public function getSuccessesCount(): int
    {
        return (int)$this->cache->get(
            $this->getKeyName(self::SUCCESSES),
            0
        );
    }

    public function isAvailable(): bool
    {
        if ($this->isOpen()) {
            return false;
        }

        return true;
    }

    protected function openCircuit(): void
    {
        $this->setOpenCircuit();
        $this->setHalfOpenCircuit();
    }

    protected function getKeyName($key = null): string
    {
        return "circuit-breaker:{$this->service}:{$key}";
    }
}
