<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Freelo\Sdk\Exception\RateLimitException;

/**
 * Rate limiter to track and handle API rate limits
 *
 * Parses rate limit headers from responses and determines if requests
 * should be delayed or retried.
 */
class RateLimiter
{
    private ?int $limit = null;
    private ?int $remaining = null;
    private ?int $resetAt = null;

    /**
     * Update rate limit information from response headers
     *
     * @param array<string, string|string[]> $headers Response headers
     */
    public function updateFromHeaders(array $headers): void
    {
        // Common rate limit header formats
        $headerMappings = [
            'limit' => ['X-RateLimit-Limit', 'RateLimit-Limit', 'X-Rate-Limit-Limit'],
            'remaining' => ['X-RateLimit-Remaining', 'RateLimit-Remaining', 'X-Rate-Limit-Remaining'],
            'reset' => ['X-RateLimit-Reset', 'RateLimit-Reset', 'X-Rate-Limit-Reset'],
        ];

        $this->limit = $this->extractHeaderValue($headers, $headerMappings['limit']);
        $this->remaining = $this->extractHeaderValue($headers, $headerMappings['remaining']);
        $this->resetAt = $this->extractHeaderValue($headers, $headerMappings['reset']);
    }

    /**
     * Extract a header value from response headers
     *
     * @param array<string, string|string[]> $headers
     * @param string[] $possibleHeaders
     * @return int|null
     */
    private function extractHeaderValue(array $headers, array $possibleHeaders): ?int
    {
        // Normalize headers to lowercase keys
        $normalizedHeaders = [];
        foreach ($headers as $key => $value) {
            $normalizedHeaders[strtolower($key)] = $value;
        }

        foreach ($possibleHeaders as $headerName) {
            $normalizedName = strtolower($headerName);
            if (isset($normalizedHeaders[$normalizedName])) {
                $value = $normalizedHeaders[$normalizedName];
                $value = is_array($value) ? $value[0] : $value;
                return is_numeric($value) ? (int)$value : null;
            }
        }

        return null;
    }

    /**
     * Check if the rate limit has been exceeded
     */
    public function isLimitExceeded(): bool
    {
        return $this->remaining !== null && $this->remaining <= 0;
    }

    /**
     * Get the number of seconds until the rate limit resets
     */
    public function getSecondsUntilReset(): int
    {
        if ($this->resetAt === null) {
            return 0;
        }

        $now = time();
        $secondsUntilReset = $this->resetAt - $now;

        return max(0, $secondsUntilReset);
    }

    /**
     * Get the current rate limit information
     *
     * @return int|null The total request limit per window
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the remaining requests in the current window
     *
     * @return int|null The number of remaining requests
     */
    public function getRemaining(): ?int
    {
        return $this->remaining;
    }

    /**
     * Get the timestamp when the rate limit resets
     *
     * @return int|null Unix timestamp when the limit resets
     */
    public function getResetAt(): ?int
    {
        return $this->resetAt;
    }

    /**
     * Check if a request should be delayed to avoid hitting rate limits
     *
     * @param int $threshold Minimum remaining requests before delaying (default: 5)
     */
    public function shouldDelay(int $threshold = 5): bool
    {
        if ($this->remaining === null) {
            return false;
        }

        return $this->remaining <= $threshold;
    }

    /**
     * Calculate delay in seconds to avoid rate limit
     *
     * @param int $threshold Minimum remaining requests before delaying
     * @return int Delay in seconds (0 if no delay needed)
     */
    public function calculateDelay(int $threshold = 5): int
    {
        if (!$this->shouldDelay($threshold)) {
            return 0;
        }

        // If we're at the limit, wait until reset
        if ($this->isLimitExceeded()) {
            return $this->getSecondsUntilReset();
        }

        // Otherwise, add a small delay proportional to how close we are to the limit
        if ($this->limit !== null && $this->remaining !== null) {
            $usage = ($this->limit - $this->remaining) / $this->limit;
            // Delay up to 5 seconds based on usage
            return (int)ceil($usage * 5);
        }

        return 0;
    }

    /**
     * Throw a RateLimitException if the limit is exceeded
     *
     * @throws RateLimitException
     */
    public function throwIfExceeded(): void
    {
        if ($this->isLimitExceeded()) {
            $exception = new RateLimitException(
                sprintf(
                    'Rate limit exceeded. Limit: %d, Remaining: %d, Resets in: %d seconds',
                    $this->limit ?? 0,
                    $this->remaining ?? 0,
                    $this->getSecondsUntilReset(),
                ),
            );
            $exception->setRetryAfter($this->getSecondsUntilReset());
            throw $exception;
        }
    }

    /**
     * Reset all rate limit information
     */
    public function reset(): void
    {
        $this->limit = null;
        $this->remaining = null;
        $this->resetAt = null;
    }
}
