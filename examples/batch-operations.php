<?php

declare(strict_types=1);

/**
 * Example: Batch Operations
 *
 * This example demonstrates how to execute multiple API operations efficiently
 * using batch requests.
 */

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: '',
    email: getenv('FREELO_EMAIL') ?: ''
);

$freelo = new Freelo($credentials);

echo "=== Batch Operations Example ===\n\n";

// Example 1: Create multiple tasks in one batch
echo "Example 1: Creating multiple tasks\n";
echo str_repeat('-', 50) . "\n";

$projectId = 'your-project-id'; // Replace with actual project ID

$results = $freelo->batch()
    ->post("/projects/{$projectId}/tasks", [
        'name' => 'Setup development environment',
        'description' => 'Install dependencies and configure IDE',
    ], 'task-1')
    ->post("/projects/{$projectId}/tasks", [
        'name' => 'Write documentation',
        'description' => 'Create README and API docs',
    ], 'task-2')
    ->post("/projects/{$projectId}/tasks", [
        'name' => 'Implement tests',
        'description' => 'Write unit and integration tests',
    ], 'task-3')
    ->execute();

echo "Total operations: " . $results->count() . "\n";
echo "Successful: " . $results->successCount() . "\n";
echo "Failed: " . $results->failureCount() . "\n\n";

// Check if all succeeded
if ($results->allSucceeded()) {
    echo "All tasks created successfully!\n\n";

    // Access specific results by key
    $task1 = $results->get('task-1');
    if ($task1 && $task1->isSuccess()) {
        $taskData = $task1->getResponse()->json();
        echo "Task 1 created with ID: " . ($taskData['id'] ?? 'unknown') . "\n";
    }
} else {
    echo "Some operations failed:\n";
    foreach ($results->failed() as $result) {
        $key = $result->getKey();
        $error = $result->getException()->getMessage();
        echo "  - {$key}: {$error}\n";
    }
}

echo "\n";

// Example 2: Mixed operations with error handling
echo "Example 2: Mixed operations\n";
echo str_repeat('-', 50) . "\n";

$batch = $freelo->batch();

// Add various operations
$batch->get('/projects', [], 'list-projects')
      ->get("/projects/{$projectId}", [], 'get-project')
      ->get("/projects/{$projectId}/tasks", [], 'list-tasks')
      ->post('/tags', ['name' => 'urgent'], 'create-tag');

// Execute with error handling
$results = $batch->execute(stopOnError: false);

// Process results
foreach ($results as $result) {
    $operation = $result->getOperation();
    $key = $operation->getKey() ?? 'unknown';

    if ($result->isSuccess()) {
        $response = $result->getResponse();
        echo "✓ {$key}: {$operation->getMethod()} {$operation->getUri()} - Success\n";
    } else {
        $exception = $result->getException();
        echo "✗ {$key}: {$operation->getMethod()} {$operation->getUri()} - Failed: {$exception->getMessage()}\n";
    }
}

echo "\n";

// Example 3: Batch with cleanup
echo "Example 3: Batch update and cleanup\n";
echo str_repeat('-', 50) . "\n";

$batch = $freelo->batch();

// Update multiple tasks and delete old ones
$batch->patch('/tasks/task-1-id', ['status' => 'completed'], 'complete-task-1')
      ->patch('/tasks/task-2-id', ['status' => 'completed'], 'complete-task-2')
      ->delete('/tasks/old-task-id', 'delete-old-task');

$results = $batch->execute();

// Convert to array for logging
$arrayResults = $results->toArray();
echo json_encode($arrayResults, JSON_PRETTY_PRINT) . "\n";

echo "\nDone!\n";
