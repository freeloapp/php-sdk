<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Http\FilterBuilder;
use Freelo\Sdk\Http\Paginator;
use Freelo\Sdk\Exception\ApiException;

/**
 * Example: Pagination
 *
 * This example demonstrates how to work with paginated results in the Freelo SDK.
 * The API returns paginated results for endpoints that can return large datasets.
 */

// Initialize SDK
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY') ?: 'your-api-key',
    email: getenv('FREELO_EMAIL') ?: 'your-email@example.com'
);

$freelo = new Freelo($credentials);

try {
    // 1. Manual pagination
    echo "=== Manual Pagination ===\n\n";

    // Get first page (page 0)
    $result = $freelo->projects()->getAll(['p' => 0]);

    echo "Total projects: " . $result->getTotal() . "\n";
    echo "Projects on this page: " . $result->getCount() . "\n";
    echo "Current page: " . $result->getPage() . "\n";
    echo "Per page: " . $result->getPerPage() . "\n";
    echo "Total pages: " . $result->getTotalPages() . "\n";
    echo "Has next page: " . ($result->hasNextPage() ? 'yes' : 'no') . "\n";
    echo "Has previous page: " . ($result->hasPreviousPage() ? 'yes' : 'no') . "\n";

    // Get items from the result
    foreach ($result->getItems() as $project) {
        echo "- [{$project->id}] {$project->name}\n";
    }

    // Navigate to next page
    if ($result->hasNextPage()) {
        echo "\nFetching next page...\n";
        $nextPage = $freelo->projects()->getAll(['p' => $result->getNextPage()]);
        echo "Page " . $nextPage->getPage() . " has " . $nextPage->getCount() . " items\n";
    }

    // 2. Using PaginatedResult as iterator
    echo "\n=== PaginatedResult as Iterator ===\n\n";

    $result = $freelo->tasks()->getAll(['p' => 0]);

    // You can iterate directly over the result
    foreach ($result as $task) {
        echo "- [{$task->id}] {$task->name}\n";
    }

    // And use count()
    echo "\nCount: " . count($result) . " tasks on this page\n";

    // 3. Automatic pagination with Paginator
    echo "\n=== Automatic Pagination with Paginator ===\n\n";

    echo "Iterating through ALL projects (across all pages):\n\n";
    $count = 0;

    foreach (Paginator::fetchAll(fn($page) => $freelo->projects()->getAll(['p' => $page])) as $project) {
        echo "- [{$project->id}] {$project->name}\n";
        $count++;

        // Safety limit for the example
        if ($count >= 10) {
            echo "\n... (showing first 10 only for demo)\n";
            break;
        }
    }

    echo "\nTotal fetched: {$count} projects\n";

    // 4. Using FilterBuilder for type-safe queries
    echo "\n=== Using FilterBuilder ===\n\n";

    $filters = FilterBuilder::create()
        ->page(0)
        ->orderBy('date_add', 'desc')
        ->stateIds([1]) // Active only
        ->build();

    $result = $freelo->projects()->getAll($filters);
    echo "Filtered result: " . $result->getCount() . " active projects\n";

    // FilterBuilder for tasks with more filters
    $taskFilters = FilterBuilder::create()
        ->page(0)
        ->projectsIds([123, 456])
        ->stateIds([1])
        ->orderBy('due_date', 'asc')
        ->build();

    echo "\nFilter array:\n";
    print_r($taskFilters);

    // 5. Checking if result is empty
    echo "\n=== Empty Check ===\n\n";

    $result = $freelo->projects()->getAll(['p' => 0]);

    if ($result->isEmpty()) {
        echo "No projects found!\n";
    } else {
        echo "First project: " . $result->first()->name . "\n";
    }
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
