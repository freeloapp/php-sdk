<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: Basic Usage
 *
 * This example demonstrates the basic usage of the Freelo SDK.
 * It covers initializing the SDK, listing projects, creating tasks,
 * and other common operations.
 */

// Initialize SDK with credentials from environment variables
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

try {
    // 1. List all projects
    echo "=== Your Projects ===\n\n";

    $projects = $freelo->projects()->list();

    if (empty($projects)) {
        echo "No projects found. Create one first!\n";
    } else {
        foreach ($projects as $project) {
            echo "- [{$project->id}] {$project->name}";
            if ($project->state && $project->state->isActive()) {
                echo " (active)";
            }
            echo "\n";
        }
    }

    // 2. Get a specific project (if we have any)
    if (!empty($projects)) {
        $projectId = $projects[0]->id;

        echo "\n=== Project Details ===\n\n";

        $project = $freelo->projects()->get($projectId);
        echo "Name: {$project->name}\n";
        if ($project->owner) {
            echo "Owner: {$project->owner->fullname}\n";
        }
        if ($project->dateAdd) {
            echo "Created: {$project->dateAdd}\n";
        }

        // 3. List tasklists in the project
        echo "\n=== Tasklists ===\n\n";

        $tasklists = $freelo->tasklists()->listInProject($projectId);

        if (empty($tasklists)) {
            echo "No tasklists found in this project.\n";
        } else {
            foreach ($tasklists as $tasklist) {
                echo "- [{$tasklist->id}] {$tasklist->name}\n";
            }
        }

        // 4. List tasks in the first tasklist (if any)
        if (!empty($tasklists)) {
            $tasklistId = $tasklists[0]->id;

            echo "\n=== Tasks in '{$tasklists[0]->name}' ===\n\n";

            $tasks = $freelo->tasks()->listInTasklist($projectId, $tasklistId);

            if (empty($tasks)) {
                echo "No tasks in this tasklist.\n";
            } else {
                foreach ($tasks as $task) {
                    echo "- [{$task->id}] {$task->name}";
                    if ($task->dueDate) {
                        echo " (due: {$task->dueDate})";
                    }
                    if ($task->state && $task->state->isFinished()) {
                        echo " [DONE]";
                    }
                    echo "\n";
                }
            }
        }
    }

    // 5. Using pagination for large result sets
    echo "\n=== All Tasks (Paginated) ===\n\n";

    $result = $freelo->tasks()->getAll(['p' => 0]);

    echo "Total tasks: {$result->getTotal()}\n";
    echo "Tasks on this page: {$result->getCount()}\n\n";

    foreach ($result->getItems() as $task) {
        echo "- {$task->name}\n";
    }

    // 6. Get users/workers
    echo "\n=== Team Members ===\n\n";

    $users = $freelo->users()->getAll(['p' => 0]);

    foreach ($users->getItems() as $user) {
        echo "- {$user->fullname}";
        if ($user->email) {
            echo " ({$user->email})";
        }
        echo "\n";
    }
} catch (ApiException $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone!\n";
