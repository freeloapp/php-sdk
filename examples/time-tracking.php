<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: Time Tracking
 *
 * This example demonstrates how to use time tracking and work reports in Freelo.
 */

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

try {
    // 1. Check current time tracking status
    echo "=== Current Time Tracking Status ===\n\n";

    $current = $freelo->timeTracking()->getCurrent();

    if ($current !== null) {
        echo "Currently tracking time on task: {$current['task_id']}\n";
        echo "Started at: {$current['started_at']}\n";
    } else {
        echo "No active time tracking.\n";
    }

    // 2. Start time tracking on a task
    echo "\n=== Start Time Tracking ===\n\n";

    // First, get a task to track time on
    $tasks = $freelo->tasks()->getAll(['p' => 0]);

    if ($tasks->isEmpty()) {
        echo "No tasks available. Create a task first.\n";
    } else {
        $task = $tasks->first();
        echo "Starting time tracking on task: {$task->name}\n";

        // Start tracking
        $uuid = $freelo->timeTracking()->start(
            taskId: $task->id,
            note: 'Working on implementation'
        );

        echo "Tracking started with UUID: {$uuid}\n";

        // Let it run for a moment (in real usage you'd work on the task)
        sleep(2);

        // 3. Stop time tracking
        echo "\n=== Stop Time Tracking ===\n\n";

        $report = $freelo->timeTracking()->stop();
        echo "Time tracking stopped.\n";
        echo "Work report created with ID: {$report->id}\n";
        echo "Minutes tracked: {$report->minutes}\n";
    }

    // 4. Create work report manually
    echo "\n=== Create Work Report Manually ===\n\n";

    if (!$tasks->isEmpty()) {
        $task = $tasks->first();

        $report = $freelo->workReports()->create($task->id, [
            'minutes' => 60,
            'date_reported' => date('Y-m-d'),
            'note' => 'Code review and testing',
        ]);

        echo "Work report created:\n";
        echo "- ID: {$report->id}\n";
        echo "- Minutes: {$report->minutes}\n";
        echo "- Hours: {$report->getHours()}\n";
        echo "- Note: {$report->note}\n";
    }

    // 5. List work reports
    echo "\n=== List Work Reports ===\n\n";

    $reports = $freelo->workReports()->getAll(['p' => 0]);

    echo "Total work reports: {$reports->getTotal()}\n\n";

    foreach ($reports->getItems() as $report) {
        echo "- [{$report->id}] {$report->getHours()}h";
        if ($report->note) {
            echo " - {$report->note}";
        }
        if ($report->user) {
            echo " (by {$report->user->fullname})";
        }
        echo "\n";
    }

    // 6. Get work reports for a specific task
    echo "\n=== Work Reports for Task ===\n\n";

    if (!$tasks->isEmpty()) {
        $task = $tasks->first();
        $taskReports = $freelo->workReports()->getForTask($task->id);

        echo "Work reports for task '{$task->name}':\n";
        foreach ($taskReports as $report) {
            echo "- {$report->getHours()} hours on {$report->dateReport}\n";
        }
    }
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
