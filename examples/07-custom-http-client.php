<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;

/**
 * Example: Custom HTTP Client
 *
 * This example demonstrates how to use a custom PSR-18 HTTP client with Freelo SDK.
 * This is useful when you need to customize timeout, proxy settings, or other HTTP options.
 */

// Initialize credentials
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

// Create a custom Guzzle client with specific configuration
$guzzle = new Client([
    'timeout' => 60,              // 60 seconds timeout
    'connect_timeout' => 10,      // 10 seconds connection timeout
    'verify' => true,             // Verify SSL certificates
    'http_errors' => false,       // Don't throw exceptions on HTTP errors

    // Optional: Add proxy configuration
    // 'proxy' => 'http://proxy.example.com:8080',

    // Optional: Add custom headers
    'headers' => [
        'X-Custom-Header' => 'My-Value',
    ],
]);

// Wrap Guzzle in PSR-18 adapter
$httpClient = new GuzzleAdapter($guzzle);

// Initialize SDK with custom HTTP client
$freelo = new Freelo($credentials, $httpClient);

echo "✓ Freelo SDK initialized with custom HTTP client\n";
echo "  Timeout: 60 seconds\n";
echo "  Connect timeout: 10 seconds\n\n";

try {
    // Test the client by fetching projects
    echo "Testing custom HTTP client...\n";
    $projects = $freelo->projects()->list();
    echo "✓ Successfully fetched " . count($projects) . " projects\n";

    // You can also provide custom request and stream factories
    echo "\n✓ Custom HTTP client is working correctly!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
