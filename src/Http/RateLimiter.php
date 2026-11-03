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
     * Understands the IETF structured headers sent by the Freelo API
     * (`RateLimit: "default";r=42;t=5` and `RateLimit-Policy: "default";q=60;w=20`)
     * and falls back to the legacy `X-RateLimit-*` / `RateLimit-Limit|Remaining|Reset` formats.
     *
     * @param array<string, string|string[]> $headers Response headers
     */
    public function updateFromHeaders(array $headers): void
    {
        $headers = $this->normalizeHeaders($headers);

        $this->limit = null;
        $this->remaining = null;
        $this->resetAt = null;

        $policyName = null;
        if (isset($headers['ratelimit'])) {
            $item = $this->selectRateLimitItem($this->parseStructuredList($headers['ratelimit']));
            if ($item !== null) {
                $policyName = $item['name'];
                $this->remaining = $item['params']['r'] ?? null;
                $resetIn = $item['params']['t'] ?? null;
                $this->resetAt = $resetIn !== null ? time() + $resetIn : null;
            }
        } else {
            $this->remaining = $this->extractHeaderValue(
                $headers,
                ['x-ratelimit-remaining', 'ratelimit-remaining', 'x-rate-limit-remaining'],
            );
            $this->resetAt = $this->extractHeaderValue(
                $headers,
                ['x-ratelimit-reset', 'ratelimit-reset', 'x-rate-limit-reset'],
            );
        }

        if (isset($headers['ratelimit-policy'])) {
            $policies = $this->parseStructuredList($headers['ratelimit-policy']);
            $this->limit = $this->selectPolicyQuota($policies, $policyName);
        }

        $this->limit ??= $this->extractHeaderValue(
            $headers,
            ['x-ratelimit-limit', 'ratelimit-limit', 'x-rate-limit-limit'],
        );
    }

    /**
     * @param array<string, string|string[]> $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $key => $value) {
            $normalized[strtolower((string)$key)] = is_array($value) ? implode(', ', $value) : $value;
        }

        return $normalized;
    }

    /**
     * Extract a legacy integer header value
     *
     * @param array<string, string> $headers Normalized headers
     * @param string[] $possibleHeaders Lowercase header names
     */
    private function extractHeaderValue(array $headers, array $possibleHeaders): ?int
    {
        foreach ($possibleHeaders as $headerName) {
            if (isset($headers[$headerName])) {
                $value = trim(explode(',', $headers[$headerName])[0]);
                return is_numeric($value) ? (int)$value : null;
            }
        }

        return null;
    }

    /**
     * Parse a structured-field list such as `"default";r=42;t=5, "burst";r=1;t=1`
     *
     * Only non-negative integer parameters are kept; anything else is ignored.
     *
     * @return list<array{name: string, params: array<string, int>}>
     */
    private function parseStructuredList(string $value): array
    {
        $items = [];
        // Split on commas outside quoted strings
        $members = preg_split('/,(?=(?:[^"]*"[^"]*")*[^"]*$)/', $value) ?: [];

        foreach ($members as $member) {
            $parts = explode(';', $member);
            $name = trim(array_shift($parts), " \t\"");
            if ($name === '') {
                continue;
            }

            $params = [];
            foreach ($parts as $part) {
                $pair = explode('=', $part, 2);
                if (count($pair) !== 2) {
                    continue;
                }
                $key = strtolower(trim($pair[0]));
                $raw = trim($pair[1]);
                if ($key !== '' && preg_match('/^\d{1,15}$/', $raw) === 1) {
                    $params[$key] = (int)$raw;
                }
            }

            $items[] = ['name' => $name, 'params' => $params];
        }

        return $items;
    }

    /**
     * Pick the most restrictive `RateLimit` item (lowest remaining quota)
     *
     * @param list<array{name: string, params: array<string, int>}> $items
     * @return array{name: string, params: array<string, int>}|null
     */
    private function selectRateLimitItem(array $items): ?array
    {
        $selected = null;
        foreach ($items as $item) {
            if (!isset($item['params']['r'])) {
                continue;
            }
            if ($selected === null || $item['params']['r'] < $selected['params']['r']) {
                $selected = $item;
            }
        }

        return $selected ?? ($items[0] ?? null);
    }

    /**
     * Get the quota (`q`) of the matching policy, or of the first policy that has one
     *
     * @param list<array{name: string, params: array<string, int>}> $policies
     */
    private function selectPolicyQuota(array $policies, ?string $policyName): ?int
    {
        foreach ($policies as $policy) {
            if ($policyName !== null && $policy['name'] === $policyName && isset($policy['params']['q'])) {
                return $policy['params']['q'];
            }
        }

        foreach ($policies as $policy) {
            if (isset($policy['params']['q'])) {
                return $policy['params']['q'];
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
     * With the structured `RateLimit` header this is now + `t`, i.e. when the quota is fully replenished.
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
