<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: Manage To-Do Lists
 *
 * This example demonstrates how to create and manage to-do lists in a project.
 */

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

try {
    // Replace with your actual project ID
    $projectId = 'your-project-id';

    echo "Fetching to-do lists...\n\n";

    // List all to-do lists in the project
    $todoLists = $freelo->todoLists()->list($projectId);
    echo "Found " . count($todoLists) . " to-do lists\n\n";

    foreach ($todoLists as $todoList) {
        echo "- {$todoList->name}\n";
        echo "  ID: {$todoList->id}\n\n";
    }

    // Create a new to-do list
    echo "Creating a new to-do list...\n";
    $newTodoList = $freelo->todoLists()->create($projectId, [
        'name' => 'Project Launch Checklist',
        'description' => 'Tasks to complete before launching the project',
    ]);

    echo "✓ To-do list created successfully!\n\n";
    echo "List ID: {$newTodoList->id}\n";
    echo "Name: {$newTodoList->name}\n";

    // Get a specific to-do list
    echo "\nFetching to-do list details...\n";
    $todoList = $freelo->todoLists()->get($newTodoList->id);
    echo "✓ Retrieved: {$todoList->name}\n";

    // Update the to-do list
    echo "\nUpdating to-do list...\n";
    $updatedList = $freelo->todoLists()->update($newTodoList->id, [
        'name' => 'Updated Project Launch Checklist',
    ]);
    echo "✓ Updated to: {$updatedList->name}\n";

    // Delete the to-do list (cleanup)
    echo "\nDeleting to-do list...\n";
    $deleted = $freelo->todoLists()->delete($newTodoList->id);
    if ($deleted) {
        echo "✓ To-do list deleted successfully\n";
    }
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
