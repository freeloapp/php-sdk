<?php

declare(strict_types=1);

namespace Freelo\Sdk\Auth;

use Psr\SimpleCache\CacheInterface;

/**
 * Manages authentication tokens with optional PSR-16 caching
 *
 * This class provides token caching to reduce authentication overhead.
 * Tokens are stored with a configurable TTL (time-to-live).
 */
class TokenManager
{
    private const DEFAULT_TTL = 3600; // 1 hour

    public function __construct(
        private readonly ?CacheInterface $cache = null,
        private readonly int $ttl = self::DEFAULT_TTL,
    ) {
    }

    /**
     * Get a cached token or generate a new one
     *
     * @param string $key Cache key for the token
     * @param callable(): string $generator Function to generate new token if not cached
     */
    public function getToken(string $key, callable $generator): string
    {
        if ($this->cache === null) {
            return $generator();
        }

        $token = $this->cache->get($key);

        if ($token !== null && is_string($token)) {
            return $token;
        }

        $token = $generator();
        $this->cache->set($key, $token, $this->ttl);

        return $token;
    }

    /**
     * Invalidate a cached token
     */
    public function invalidateToken(string $key): void
    {
        $this->cache?->delete($key);
    }

    /**
     * Clear all cached tokens
     */
    public function clearAll(): void
    {
        $this->cache?->clear();
    }
}
