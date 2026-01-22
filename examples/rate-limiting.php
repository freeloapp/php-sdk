<?php

declare(strict_types=1);

/**
 * Example: Rate Limiting and Retry
 *
 * This example demonstrates how to handle rate limiting and implement
 * automatic retry with exponential backoff.
 */

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\RetryHandler;
use Freelo\Sdk\Http\ClientFactory;
use Freelo\Sdk\Exception\RateLimitException;

// Initialize credentials
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: '',
    email: getenv('FREELO_EMAIL') ?: ''
);

echo "=== Rate Limiting Example ===\n\n";

// Example 1: Basic rate limit checking
echo "Example 1: Checking rate limit status\n";
echo str_repeat('-', 50) . "\n";

$freelo = new Freelo($credentials);

// Make a request
$projects = $freelo->projects()->list();
echo "Fetched " . count($projects) . " projects\n\n";

// Check rate limit info
$client = $freelo->getClient();
$rateLimiter = $client->getRateLimiter();

$limit = $rateLimiter->getLimit();
$remaining = $rateLimiter->getRemaining();
$resetAt = $rateLimiter->getResetAt();

if ($limit !== null) {
    echo "Rate Limit Status:\n";
    echo "  Total limit: {$limit}\n";
    echo "  Remaining: {$remaining}\n";
    echo "  Resets at: " . ($resetAt ? date('Y-m-d H:i:s', $resetAt) : 'unknown') . "\n";
    echo "  Seconds until reset: " . $rateLimiter->getSecondsUntilReset() . "\n\n";

    // Check if we should delay
    if ($rateLimiter->shouldDelay(threshold: 10)) {
        $delay = $rateLimiter->calculateDelay();
        echo "Close to rate limit. Suggested delay: {$delay} seconds\n";
    }
} else {
    echo "No rate limit information available\n";
}

echo "\n";

// Example 2: Automatic retry with exponential backoff
echo "Example 2: Automatic retry\n";
echo str_repeat('-', 50) . "\n";

// Create retry handler
$retryHandler = new RetryHandler(
    maxRetries: 3,      // Retry up to 3 times
    initialDelay: 1,    // Start with 1 second delay
    maxDelay: 60,       // Maximum 60 seconds delay
    multiplier: 2       // Double the delay each time
);

echo "Retry Handler Configuration:\n";
echo "  Max retries: " . $retryHandler->getMaxRetries() . "\n";
echo "  Initial delay: " . $retryHandler->getInitialDelay() . " seconds\n";
echo "  Max delay: " . $retryHandler->getMaxDelay() . " seconds\n";
echo "  Multiplier: " . $retryHandler->getMultiplier() . "\n\n";

// Create client with retry handler
$httpClient = ClientFactory::createClient();
$requestFactory = ClientFactory::createRequestFactory();
$streamFactory = ClientFactory::createStreamFactory();

$clientWithRetry = new FreeloClient(
    $httpClient,
    $requestFactory,
    $streamFactory,
    $credentials,
    retryHandler: $retryHandler
);

echo "Making requests with automatic retry...\n";

try {
    // This will automatically retry on transient failures
    $response = $clientWithRetry->get('/projects');
    echo "✓ Request succeeded\n";
} catch (\Exception $e) {
    echo "✗ Request failed after retries: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Custom retry logic
echo "Example 3: Custom retry logic\n";
echo str_repeat('-', 50) . "\n";

$retryHandler = new RetryHandler(maxRetries: 5);

try {
    $result = $retryHandler->execute(
        callable: function () use ($freelo) {
            echo "  Attempting request...\n";
            return $freelo->projects()->list();
        },
        shouldRetry: function (int $attempt, \Throwable $e): bool {
            echo "  Attempt {$attempt} failed: " . get_class($e) . "\n";

            // Custom logic: only retry rate limit errors
            if ($e instanceof RateLimitException) {
                $retryAfter = $e->getRetryAfter();
                echo "  Rate limited. Will retry after {$retryAfter} seconds\n";
                return true;
            }

            // Don't retry other errors
            return false;
        }
    );

    echo "✓ Request succeeded with " . count($result) . " projects\n";

} catch (\Exception $e) {
    echo "✗ Request failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Handle rate limit exception
echo "Example 4: Handling rate limit exceptions\n";
echo str_repeat('-', 50) . "\n";

try {
    // Check if we're at the rate limit
    $rateLimiter->throwIfExceeded();
    echo "✓ Rate limit not exceeded, safe to proceed\n";

} catch (RateLimitException $e) {
    echo "✗ Rate limit exceeded!\n";
    echo "  Message: " . $e->getMessage() . "\n";
    echo "  Retry after: " . $e->getRetryAfter() . " seconds\n";
    echo "  Status code: " . $e->getStatusCode() . "\n";
}

echo "\nDone!\n";
