<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Exception\AuthenticationException;
use Freelo\Sdk\Exception\NotFoundException;
use Freelo\Sdk\Exception\RateLimitException;
use Freelo\Sdk\Exception\ValidationException;

/**
 * Example: Error Handling
 *
 * This example demonstrates how to handle different types of errors
 * that can occur when using the Freelo SDK.
 */

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

echo "Demonstrating error handling in Freelo SDK\n\n";

// Example 1: Handling not found errors
echo "1. Handling NotFoundException:\n";
try {
    // Try to get a non-existent project
    $project = $freelo->projects()->get('non-existent-id-12345');
    echo "   Project found: {$project->name}\n";
} catch (NotFoundException $e) {
    echo "   ✓ Caught NotFoundException: {$e->getMessage()}\n";
    echo "   Status code: {$e->getCode()}\n";
} catch (ApiException $e) {
    echo "   API Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 2: Handling authentication errors
echo "2. Handling AuthenticationException:\n";
try {
    // Try with invalid credentials
    $invalidCredentials = new ApiKeyCredentials(
        apiKey: 'invalid-key',
        email: 'invalid@example.com'
    );
    $invalidFreelo = new Freelo($invalidCredentials);
    $invalidFreelo->projects()->list();
} catch (AuthenticationException $e) {
    echo "   ✓ Caught AuthenticationException: {$e->getMessage()}\n";
} catch (ApiException $e) {
    echo "   ✓ Caught API error (authentication failed): {$e->getMessage()}\n";
}

echo "\n";

// Example 3: Handling validation errors
echo "3. Handling ValidationException:\n";
try {
    // Try to create a project with invalid data
    $project = $freelo->projects()->create([
        // Missing required 'name' field
        'description' => 'This will fail validation',
    ]);
} catch (ValidationException $e) {
    echo "   ✓ Caught ValidationException: {$e->getMessage()}\n";
} catch (ApiException $e) {
    echo "   ✓ Caught API error (validation failed): {$e->getMessage()}\n";
}

echo "\n";

// Example 4: Handling rate limit errors
echo "4. Handling RateLimitException:\n";
echo "   (This example demonstrates the exception type, actual rate limiting depends on API)\n";
try {
    // Make multiple requests rapidly
    for ($i = 0; $i < 5; $i++) {
        $freelo->projects()->list();
    }
    echo "   ✓ All requests completed successfully\n";
} catch (RateLimitException $e) {
    echo "   ✓ Caught RateLimitException: {$e->getMessage()}\n";
    echo "   Retry after: {$e->getRetryAfter()} seconds\n";
} catch (ApiException $e) {
    echo "   API Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 5: Generic error handling with try-catch hierarchy
echo "5. Generic error handling pattern:\n";
try {
    $projectId = 'your-project-id';

    // Create a task
    $task = $freelo->tasks()->create($projectId, [
        'name' => 'Test task',
        'description' => 'This is a test',
    ]);

    echo "   ✓ Task created: {$task->name}\n";
} catch (NotFoundException $e) {
    echo "   ✗ Resource not found: {$e->getMessage()}\n";
} catch (AuthenticationException $e) {
    echo "   ✗ Authentication failed: {$e->getMessage()}\n";
} catch (ValidationException $e) {
    echo "   ✗ Validation error: {$e->getMessage()}\n";
} catch (RateLimitException $e) {
    echo "   ✗ Rate limit exceeded: {$e->getMessage()}\n";
    echo "   Retry after {$e->getRetryAfter()} seconds\n";
} catch (ApiException $e) {
    // Catch all other API exceptions
    echo "   ✗ API error: {$e->getMessage()}\n";
    echo "   Status code: {$e->getCode()}\n";
} catch (\Exception $e) {
    // Catch any other exceptions
    echo "   ✗ Unexpected error: {$e->getMessage()}\n";
}

echo "\n✓ Error handling demonstration completed\n";
