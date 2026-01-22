<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Enum\Priority;

/**
 * Example: Create a Task
 *
 * This example demonstrates how to create a new task in a project.
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

    echo "Creating a new task...\n\n";

    // Create a task
    $task = $freelo->tasks()->create($projectId, [
        'name' => 'Implement new feature',
        'description' => 'This is a detailed description of the task.',
        'priority' => Priority::High->value,
        'due_date' => date('Y-m-d', strtotime('+7 days')),
    ]);

    echo "✓ Task created successfully!\n\n";
    echo "Task ID: {$task->id}\n";
    echo "Name: {$task->name}\n";
    echo "Description: {$task->description}\n";
    echo "Priority: {$task->priority}\n";
    echo "Due Date: {$task->dueDate}\n";

    // Add a comment to the task
    echo "\nAdding a comment...\n";
    $comment = $freelo->tasks()->addComment($task->id, 'This is a comment on the task.');

    echo "✓ Comment added: {$comment->content}\n";

    // Set priority
    echo "\nUpdating priority...\n";
    $freelo->tasks()->setPriority($task->id, Priority::Urgent->value);
    echo "✓ Priority updated to Urgent\n";
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
