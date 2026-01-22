<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Example: Using Cache for Authentication Tokens
 *
 * This example demonstrates how to use PSR-16 cache to store authentication tokens
 * and reduce the number of authentication requests to the API.
 *
 * Note: You need to install a PSR-16 cache implementation:
 * composer require symfony/cache
 */

// Initialize credentials
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

// Create a PSR-16 cache implementation
// This example uses Symfony Cache with filesystem adapter
$filesystemAdapter = new FilesystemAdapter(
    namespace: 'freelo_sdk',
    defaultLifetime: 3600,  // 1 hour
    directory: sys_get_temp_dir() . '/freelo-cache'
);

$cache = new Psr16Cache($filesystemAdapter);

// Initialize SDK with cache
$freelo = new Freelo(
    credentials: $credentials,
    cache: $cache
);

echo "✓ Freelo SDK initialized with cache\n";
echo "  Cache directory: " . sys_get_temp_dir() . "/freelo-cache\n\n";

try {
    // First request - will authenticate and cache the token
    echo "Making first request (will authenticate)...\n";
    $startTime = microtime(true);
    $projects = $freelo->projects()->list();
    $firstRequestTime = microtime(true) - $startTime;

    echo "✓ Fetched " . count($projects) . " projects\n";
    echo "  Time: " . round($firstRequestTime * 1000, 2) . "ms\n\n";

    // Second request - will use cached token
    echo "Making second request (will use cached token)...\n";
    $startTime = microtime(true);
    $projects = $freelo->projects()->list();
    $secondRequestTime = microtime(true) - $startTime;

    echo "✓ Fetched " . count($projects) . " projects\n";
    echo "  Time: " . round($secondRequestTime * 1000, 2) . "ms\n\n";

    // Check cache instance
    $cacheInstance = $freelo->getCache();
    if ($cacheInstance !== null) {
        echo "✓ Cache is active and working\n";

        // Calculate performance improvement
        if ($firstRequestTime > $secondRequestTime) {
            $improvement = (($firstRequestTime - $secondRequestTime) / $firstRequestTime) * 100;
            echo "  Performance improvement: " . round($improvement, 1) . "%\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
