<?php

declare(strict_types=1);

namespace Freelo\Sdk;

/**
 * Configuration constants for Freelo SDK
 */
final class Config
{
    /**
     * Default Freelo API base URL
     */
    public const API_BASE_URL = 'https://api.freelo.io/v1';

    /**
     * API version
     */
    public const API_VERSION = 'v1';

    /**
     * Default timeout for HTTP requests (in seconds)
     */
    public const DEFAULT_TIMEOUT = 30;

    /**
     * User agent string
     */
    public const USER_AGENT = 'Freelo-PHP-SDK/1.0';

    /**
     * Default cache TTL for tokens (in seconds)
     */
    public const TOKEN_CACHE_TTL = 3600; // 1 hour

    private function __construct()
    {
        // Prevent instantiation
    }
}
