<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Exception\RateLimitException;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Handles automatic retries with exponential backoff
 *
 * Implements retry logic for transient failures and rate limit errors.
 */
class RetryHandler
{
    private const DEFAULT_MAX_RETRIES = 3;
    private const DEFAULT_INITIAL_DELAY = 1; // seconds
    private const DEFAULT_MAX_DELAY = 60; // seconds
    private const DEFAULT_MULTIPLIER = 2;

    /**
     * @param int $maxRetries Maximum number of retry attempts
     * @param int $initialDelay Initial delay in seconds
     * @param int $maxDelay Maximum delay in seconds
     * @param int $multiplier Backoff multiplier
     */
    public function __construct(
        private readonly int $maxRetries = self::DEFAULT_MAX_RETRIES,
        private readonly int $initialDelay = self::DEFAULT_INITIAL_DELAY,
        private readonly int $maxDelay = self::DEFAULT_MAX_DELAY,
        private readonly int $multiplier = self::DEFAULT_MULTIPLIER,
    ) {
    }

    /**
     * Execute a callable with retry logic
     *
     * @template T
     * @param callable(): T $callable The function to execute
     * @param callable(int, \Throwable): bool|null $shouldRetry Optional callback to determine if retry should happen
     * @return T The result of the callable
     * @throws \Throwable If all retries fail
     */
    public function execute(callable $callable, ?callable $shouldRetry = null): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt <= $this->maxRetries) {
            try {
                return $callable();
            } catch (\Throwable $e) {
                $lastException = $e;

                // Don't retry if we've exhausted all attempts
                if ($attempt >= $this->maxRetries) {
                    throw $e;
                }

                // Check if we should retry this exception
                if ($shouldRetry !== null && !$shouldRetry($attempt, $e)) {
                    throw $e;
                }

                // Default retry logic
                if (!$this->isRetriable($e)) {
                    throw $e;
                }

                // Calculate delay
                $delay = $this->calculateDelay($attempt, $e);

                // Sleep before retry
                if ($delay > 0) {
                    sleep($delay);
                }

                $attempt++;
            }
        }

        // This should never be reached, but just in case
        throw $lastException ?? new ApiException('Retry failed with no exception');
    }

    /**
     * Determine if an exception is retriable
     */
    private function isRetriable(\Throwable $exception): bool
    {
        // Rate limit exceptions are retriable
        if ($exception instanceof RateLimitException) {
            return true;
        }

        // Network/client exceptions are retriable
        if ($exception instanceof ClientExceptionInterface) {
            return true;
        }

        // API exceptions with specific status codes are retriable
        if ($exception instanceof ApiException) {
            $statusCode = $exception->getStatusCode();

            // Retry on server errors (5xx) and some client errors
            return in_array($statusCode, [408, 429, 500, 502, 503, 504], true);
        }

        return false;
    }

    /**
     * Calculate the delay for the next retry
     */
    private function calculateDelay(int $attempt, \Throwable $exception): int
    {
        // If rate limited, use the retry-after value
        if ($exception instanceof RateLimitException) {
            $retryAfter = $exception->getRetryAfter();
            if ($retryAfter !== null) {
                return min($retryAfter, $this->maxDelay);
            }
        }

        // Exponential backoff: initialDelay * (multiplier ^ attempt)
        $delay = $this->initialDelay * ($this->multiplier ** $attempt);

        // Add jitter (random ±25%) to avoid thundering herd
        $jitter = $delay * 0.25;
        $delay = $delay + random_int((int)-$jitter, (int)$jitter);

        // Cap at max delay
        return min((int)$delay, $this->maxDelay);
    }

    /**
     * Get the maximum number of retries
     */
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * Get the initial delay
     */
    public function getInitialDelay(): int
    {
        return $this->initialDelay;
    }

    /**
     * Get the maximum delay
     */
    public function getMaxDelay(): int
    {
        return $this->maxDelay;
    }

    /**
     * Get the backoff multiplier
     */
    public function getMultiplier(): int
    {
        return $this->multiplier;
    }
}
