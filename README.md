# Freelo PHP SDK

[![PHP Version](https://img.shields.io/packagist/php-v/freeloapp/php-sdk.svg?style=flat-square)](https://packagist.org/packages/freeloapp/php-sdk)
[![License](https://img.shields.io/packagist/l/freeloapp/php-sdk.svg?style=flat-square)](LICENSE)

Modern, lightweight PHP SDK for [Freelo API](https://freelo.docs.apiary.io/).

## Features

- PHP 8.1+ with strict types
- PSR-18/17/7/16 compliant (bring your own HTTP client)
- Full API coverage (projects, tasks, files, comments, time tracking, etc.)
- Dynamic credentials with server-safe per-request switching
- Low-level `call()` method for arbitrary API endpoints
- Automatic pagination support
- Rate limiting detection
- Typed exceptions for error handling

## Installation

```bash
composer require freeloapp/php-sdk
```

Install an HTTP client (e.g., Guzzle):

```bash
composer require guzzlehttp/guzzle php-http/guzzle7-adapter nyholm/psr7
```

## Quick Start

```php
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Auth\ApiKeyCredentials;

$freelo = new Freelo(
    new ApiKeyCredentials('your-api-key', 'your-email@example.com'),
    userAgent: 'MyApp/1.0',
);

// List projects
foreach ($freelo->projects()->list() as $project) {
    echo $project->name . "\n";
}

// Create a task
$task = $freelo->tasks()->create(
    projectId: 123,
    tasklistId: 456,
    data: ['name' => 'New task', 'priority_enum' => 'h']
);
```

## Authentication

Get your API key from [Freelo Settings](https://app.freelo.io/profil/nastaveni) (scroll to API section).

```php
use Freelo\Sdk\Auth\ApiKeyCredentials;

// Using environment variables (recommended)
$credentials = new ApiKeyCredentials(
    apiKey: getenv('FREELO_API_KEY'),
    email: getenv('FREELO_EMAIL')
);

$freelo = new Freelo($credentials, userAgent: 'MyApp/1.0');
```

## Usage Examples

### Projects

```php
$projects = $freelo->projects()->list();           // List active projects
$project = $freelo->projects()->get(123);          // Get project by ID
$freelo->projects()->archive(123);                 // Archive project
$freelo->projects()->activate(123);                // Activate archived project

// Paginated results
$result = $freelo->projects()->getAll(['p' => 0]);
echo "Total: " . $result->getTotal();
```

### Tasks

```php
$tasks = $freelo->tasks()->listInTasklist(123, 456);  // List tasks in tasklist
$task = $freelo->tasks()->get(789);                    // Get task by ID
$freelo->tasks()->finish(789);                         // Mark as complete
$freelo->tasks()->activate(789);                       // Reopen task
$freelo->tasks()->move(789, 999);                      // Move to another tasklist
$freelo->tasks()->addComment(789, 'Comment text');     // Add comment
```

### Time Tracking

```php
$freelo->timeTracking()->start(taskId: 123, note: 'Working');
$report = $freelo->timeTracking()->stop();

// Manual work report
$freelo->workReports()->create(123, [
    'minutes' => 120,
    'date_reported' => '2024-01-15',
    'note' => 'Development work',
]);
```

### Files

```php
$freelo->files()->uploadToTask(taskId: 123, filePath: '/path/to/file.pdf');
$freelo->files()->download(fileId: 456, destination: '/path/to/save.pdf');
```

### Dynamic Credentials

#### Per-request credentials (server-safe)

Use `withCredentials()` to create an isolated instance with different credentials. Each derived instance has its own client and state — safe for concurrent requests in multi-tenant server applications.

```php
// Shared base instance (e.g. in a service provider)
$freelo = new Freelo(new ApiKeyCredentials(
    apiKey: 'default-key',
    email: 'default@example.com'
));

// Per-request — fully isolated, concurrency-safe
$userFreelo = $freelo->withCredentials(new ApiKeyCredentials(
    apiKey: $user->apiKey,
    email: $user->email
));

// Optionally override userAgent per tenant
$userFreelo = $freelo->withCredentials(
    new ApiKeyCredentials($user->apiKey, $user->email),
    userAgent: 'TenantApp/1.0',
);

$projects = $userFreelo->projects()->list();
```

For simple single-user scripts, you can swap credentials in place with `setCredentials()`:

```php
$freelo->setCredentials(new ApiKeyCredentials(
    apiKey: 'new-key',
    email: 'new@example.com'
));

// With userAgent override
$freelo->setCredentials(
    new ApiKeyCredentials('new-key', 'new@example.com'),
    userAgent: 'MyApp/2.0',
);
```

> **Note:** `setCredentials()` mutates the shared instance and is **not safe** for concurrent requests.

#### Lazy initialization

The SDK supports creating a client without credentials upfront. Credentials can be provided later via `setCredentials()` — validation is deferred to the first API request.

```php
$freelo = new Freelo();

// ... later, when credentials are available
$freelo->setCredentials(new ApiKeyCredentials(
    apiKey: $apiKey,
    email: $email
));

$freelo->projects()->list(); // works
```

### Low-level API Calls

Use `call()` to hit arbitrary API endpoints not yet covered by typed resource classes:

```php
// GET request with query parameters
$response = $freelo->call('/projects', 'GET', params: ['p' => 0]);
$data = $response->json();

// POST request with body
$response = $freelo->call('/projects/123/tasklists/456/tasks', 'POST', data: [
    'name' => 'New task',
    'priority_enum' => 'h',
]);

// Also available on derived instances
$userFreelo = $freelo->withCredentials($credentials);
$response = $userFreelo->call('/users/me', 'GET');
```

## Available Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Projects | `projects()` | Project management |
| Tasks | `tasks()` | Task management |
| Tasklists | `tasklists()` | Tasklist management |
| Comments | `comments()` | Comment management |
| Files | `files()` | File upload/download |
| Task Labels | `taskLabels()` | Task label management |
| Project Labels | `projectLabels()` | Project label management |
| Subtasks | `subtasks()` | Subtask management |
| Time Tracking | `timeTracking()` | Time tracking |
| Work Reports | `workReports()` | Work report management |
| Users | `users()` | User management |
| Notifications | `notifications()` | Notifications |
| Events | `events()` | Activity events |
| Notes | `notes()` | Note management |
| Search | `search()` | Full-text search |
| Custom Fields | `customFields()` | Custom fields |
| Invoices | `invoices()` | Invoice management |

## Pagination

```php
use Freelo\Sdk\Http\Paginator;

// Auto-paginate through all results
foreach (Paginator::fetchAll(fn($p) => $freelo->projects()->getAll(['p' => $p])) as $project) {
    echo $project->name . "\n";
}

// Manual pagination
$page = $freelo->tasks()->getAll(['p' => 0]);
echo "Page {$page->getPage()} of " . ceil($page->getTotal() / 20);
if ($page->hasNextPage()) {
    $nextPage = $freelo->tasks()->getAll(['p' => $page->getNextPage()]);
}
```

## Error Handling

```php
use Freelo\Sdk\Exception\{
    ApiException,
    AuthenticationException,
    NotFoundException,
    RateLimitException,
    ValidationException
};

try {
    $project = $freelo->projects()->get(999);
} catch (NotFoundException $e) {
    // Resource not found (404)
} catch (AuthenticationException $e) {
    // Invalid credentials (401)
} catch (RateLimitException $e) {
    // Rate limited (429) - retry after $e->getRetryAfter() seconds
} catch (ValidationException $e) {
    // Invalid input (422)
} catch (ApiException $e) {
    // Other API errors
}
```

## Rate Limiting

The API allows 25 requests/minute. The SDK tracks limits automatically:

```php
$limiter = $freelo->getClient()->getRateLimiter();
if ($limiter->isLimitExceeded()) {
    sleep($limiter->getSecondsUntilReset());
}
```

## Custom HTTP Client

```php
use GuzzleHttp\Client;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client(['timeout' => 30]));
$freelo = new Freelo($credentials, httpClient: $httpClient);
```

## Caching (Optional)

```php
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$cache = new Psr16Cache(new FilesystemAdapter('freelo', 3600));
$freelo = new Freelo($credentials, cache: $cache);
```

## Development

```bash
composer install     # Install dependencies
composer test        # Run tests
composer phpstan     # Static analysis
composer check       # Run all checks
```

## Requirements

- PHP 8.1+
- PSR-18 HTTP Client (e.g., Guzzle)
- PSR-17 HTTP Factories (e.g., nyholm/psr7)

## License

MIT License. See [LICENSE](LICENSE).

## Links

- [Freelo API Documentation](https://freelo.docs.apiary.io/)
- [Freelo Help - API](https://help.freelo.io/en/help/api/)
