<?php

declare(strict_types=1);

namespace Freelo\Sdk;

use Freelo\Sdk\Auth\Credentials;
use Freelo\Sdk\Batch\BatchRequest;
use Freelo\Sdk\Http\ClientFactory;
use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Resource\CommentResource;
use Freelo\Sdk\Resource\CustomFieldResource;
use Freelo\Sdk\Resource\EventResource;
use Freelo\Sdk\Resource\FileResource;
use Freelo\Sdk\Resource\InvoiceResource;
use Freelo\Sdk\Resource\NoteResource;
use Freelo\Sdk\Resource\NotificationResource;
use Freelo\Sdk\Resource\PinnedItemResource;
use Freelo\Sdk\Resource\ProjectLabelResource;
use Freelo\Sdk\Resource\ProjectResource;
use Freelo\Sdk\Resource\SearchResource;
use Freelo\Sdk\Resource\StateResource;
use Freelo\Sdk\Resource\SubtaskResource;
use Freelo\Sdk\Resource\TaskLabelResource;
use Freelo\Sdk\Resource\TasklistResource;
use Freelo\Sdk\Resource\TaskResource;
use Freelo\Sdk\Resource\TimeTrackingResource;
use Freelo\Sdk\Resource\UserResource;
use Freelo\Sdk\Resource\WorkReportResource;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Main Freelo SDK facade
 *
 * This is the main entry point for interacting with the Freelo API.
 *
 * Example usage:
 * ```php
 * $credentials = new ApiKeyCredentials('api-key', 'email@example.com');
 * $freelo = new Freelo($credentials);
 *
 * $projects = $freelo->projects()->list();
 * $task = $freelo->tasks()->create('project-id', ['name' => 'New task']);
 * ```
 */
class Freelo
{
    private readonly FreeloClient $client;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;

    private ?ProjectResource $projectResource = null;
    private ?TaskResource $taskResource = null;
    private ?TasklistResource $tasklistResource = null;
    private ?FileResource $fileResource = null;
    private ?TaskLabelResource $taskLabelResource = null;
    private ?CommentResource $commentResource = null;
    private ?ProjectLabelResource $projectLabelResource = null;
    private ?PinnedItemResource $pinnedItemResource = null;
    private ?SubtaskResource $subtaskResource = null;
    private ?TimeTrackingResource $timeTrackingResource = null;
    private ?WorkReportResource $workReportResource = null;
    private ?InvoiceResource $invoiceResource = null;
    private ?UserResource $userResource = null;
    private ?NotificationResource $notificationResource = null;
    private ?EventResource $eventResource = null;
    private ?StateResource $stateResource = null;
    private ?CustomFieldResource $customFieldResource = null;
    private ?NoteResource $noteResource = null;
    private ?SearchResource $searchResource = null;

    public function __construct(
        private ?Credentials $credentials = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        private readonly ?CacheInterface $cache = null,
        ?string $userAgent = null,
    ) {
        // Use provided clients or discover them
        $this->httpClient = $httpClient ?? ClientFactory::createClient();
        $this->requestFactory = $requestFactory ?? ClientFactory::createRequestFactory();
        $this->streamFactory = $streamFactory ?? ClientFactory::createStreamFactory();

        // Create the HTTP client
        $this->client = new FreeloClient(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            $this->credentials,
        );

        if ($userAgent !== null) {
            $this->client->setUserAgent($userAgent);
        }
    }

    /**
     * Get project resource manager
     */
    public function projects(): ProjectResource
    {
        return $this->projectResource ??= new ProjectResource($this->client);
    }

    /**
     * Get task resource manager
     */
    public function tasks(): TaskResource
    {
        return $this->taskResource ??= new TaskResource($this->client);
    }

    /**
     * Get tasklist resource manager
     */
    public function tasklists(): TasklistResource
    {
        return $this->tasklistResource ??= new TasklistResource($this->client);
    }

    /**
     * Get file resource manager
     */
    public function files(): FileResource
    {
        return $this->fileResource ??= new FileResource($this->client);
    }

    /**
     * Get task label resource manager
     */
    public function taskLabels(): TaskLabelResource
    {
        return $this->taskLabelResource ??= new TaskLabelResource($this->client);
    }

    /**
     * Get comment resource manager
     */
    public function comments(): CommentResource
    {
        return $this->commentResource ??= new CommentResource($this->client);
    }

    /**
     * Get project label resource manager
     */
    public function projectLabels(): ProjectLabelResource
    {
        return $this->projectLabelResource ??= new ProjectLabelResource($this->client);
    }

    /**
     * Get pinned item resource manager
     */
    public function pinnedItems(): PinnedItemResource
    {
        return $this->pinnedItemResource ??= new PinnedItemResource($this->client);
    }

    /**
     * Get subtask resource manager
     */
    public function subtasks(): SubtaskResource
    {
        return $this->subtaskResource ??= new SubtaskResource($this->client);
    }

    /**
     * Get time tracking resource manager
     */
    public function timeTracking(): TimeTrackingResource
    {
        return $this->timeTrackingResource ??= new TimeTrackingResource($this->client);
    }

    /**
     * Get work report resource manager
     */
    public function workReports(): WorkReportResource
    {
        return $this->workReportResource ??= new WorkReportResource($this->client);
    }

    /**
     * Get invoice resource manager
     */
    public function invoices(): InvoiceResource
    {
        return $this->invoiceResource ??= new InvoiceResource($this->client);
    }

    /**
     * Get user resource manager
     */
    public function users(): UserResource
    {
        return $this->userResource ??= new UserResource($this->client);
    }

    /**
     * Get notification resource manager
     */
    public function notifications(): NotificationResource
    {
        return $this->notificationResource ??= new NotificationResource($this->client);
    }

    /**
     * Get event resource manager
     */
    public function events(): EventResource
    {
        return $this->eventResource ??= new EventResource($this->client);
    }

    /**
     * Get state resource manager
     */
    public function states(): StateResource
    {
        return $this->stateResource ??= new StateResource($this->client);
    }

    /**
     * Get custom field resource manager
     */
    public function customFields(): CustomFieldResource
    {
        return $this->customFieldResource ??= new CustomFieldResource($this->client);
    }

    /**
     * Get note resource manager
     */
    public function notes(): NoteResource
    {
        return $this->noteResource ??= new NoteResource($this->client);
    }

    /**
     * Get search resource manager
     */
    public function search(): SearchResource
    {
        return $this->searchResource ??= new SearchResource($this->client);
    }

    /**
     * Set custom API base URL
     */
    public function setApiUrl(string $url): self
    {
        $this->client->setBaseUrl($url);
        return $this;
    }

    /**
     * Get current API base URL
     */
    public function getApiUrl(): string
    {
        return $this->client->getBaseUrl();
    }

    /**
     * Set custom user agent
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->client->setUserAgent($userAgent);
        return $this;
    }

    /**
     * Get current user agent
     */
    public function getUserAgent(): string
    {
        return $this->client->getUserAgent();
    }

    /**
     * Get the underlying HTTP client
     */
    public function getClient(): FreeloClient
    {
        return $this->client;
    }

    /**
     * Get the cache instance (if provided)
     */
    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    /**
     * Get credentials (may be null if using lazy initialization)
     */
    public function getCredentials(): ?Credentials
    {
        return $this->credentials;
    }

    /**
     * Set credentials for authentication
     *
     * Replaces the current credentials in place. This mutates the shared client
     * instance, so all resource namespaces will immediately use the new credentials
     * on subsequent requests.
     *
     * Note: This is not safe for concurrent requests. For concurrency-safe
     * credential switching, use withCredentials() instead.
     */
    public function setCredentials(Credentials $credentials, ?string $userAgent = null): self
    {
        $this->credentials = $credentials;
        $this->client->setCredentials($credentials, $userAgent);
        return $this;
    }

    /**
     * Create a new Freelo instance with different credentials
     *
     * Returns a brand-new independent instance that inherits the current
     * configuration (base URL, user agent, HTTP client, cache) but uses
     * the provided credentials. The new instance has its own resource
     * namespace objects and does not share state with the parent.
     *
     * This is the concurrency-safe approach for multi-tenant or per-request
     * credential switching.
     */
    public function withCredentials(Credentials $credentials, ?string $userAgent = null): self
    {
        $instance = new self(
            $credentials,
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            $this->cache,
        );

        // Inherit configuration from the parent
        $instance->setApiUrl($this->getApiUrl());
        $instance->setUserAgent($userAgent ?? $this->getUserAgent());

        return $instance;
    }

    /**
     * Low-level method for calling arbitrary API endpoints
     *
     * This is an escape hatch for hitting API endpoints that are not yet
     * covered by the typed resource classes, or for advanced use cases
     * where direct control over the request is needed.
     *
     * @param string $path Endpoint path (e.g., '/projects' or 'projects/123/tasks')
     * @param string $method HTTP method ('GET', 'POST', 'PUT', 'PATCH', 'DELETE')
     * @param array<string, mixed> $data Request body data (sent as JSON)
     * @param array<string, mixed> $params Query parameters
     * @return Response
     */
    public function call(string $path, string $method, array $data = [], array $params = []): Response
    {
        $options = [];

        if (!empty($params)) {
            $options['query'] = $params;
        }

        if (!empty($data)) {
            $options['json'] = $data;
        }

        return $this->client->request($method, $path, $options);
    }

    /**
     * Create a new batch request
     *
     * Allows queuing multiple API operations for efficient execution.
     *
     * Example:
     * ```php
     * $results = $freelo->batch()
     *     ->get('/projects', [], 'projects')
     *     ->post('/projects/123/tasks', ['name' => 'Task 1'], 'task1')
     *     ->post('/projects/123/tasks', ['name' => 'Task 2'], 'task2')
     *     ->execute();
     *
     * if ($results->allSucceeded()) {
     *     echo "All operations succeeded!";
     * }
     * ```
     */
    public function batch(): BatchRequest
    {
        return new BatchRequest($this->client);
    }
}
