<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit;

use Freelo\Sdk\Auth\ApiKeyCredentials;
use Freelo\Sdk\Freelo;
use Freelo\Sdk\Resource\CommentResource;
use Freelo\Sdk\Resource\FileResource;
use Freelo\Sdk\Resource\ProjectResource;
use Freelo\Sdk\Resource\TaskLabelResource;
use Freelo\Sdk\Resource\TaskResource;
use Freelo\Sdk\Resource\TasklistResource;
use PHPUnit\Framework\TestCase;

class FreeloTest extends TestCase
{
    private Freelo $freelo;

    protected function setUp(): void
    {
        $credentials = new ApiKeyCredentials('test-api-key', 'test@example.com');
        $this->freelo = new Freelo($credentials);
    }

    public function testProjectsReturnsProjectResource(): void
    {
        $resource = $this->freelo->projects();

        $this->assertInstanceOf(ProjectResource::class, $resource);
        $this->assertSame($resource, $this->freelo->projects(), 'Should return same instance');
    }

    public function testTasksReturnsTaskResource(): void
    {
        $resource = $this->freelo->tasks();

        $this->assertInstanceOf(TaskResource::class, $resource);
        $this->assertSame($resource, $this->freelo->tasks(), 'Should return same instance');
    }

    public function testTasklistsReturnsTasklistResource(): void
    {
        $resource = $this->freelo->tasklists();

        $this->assertInstanceOf(TasklistResource::class, $resource);
        $this->assertSame($resource, $this->freelo->tasklists(), 'Should return same instance');
    }

    public function testFilesReturnsFileResource(): void
    {
        $resource = $this->freelo->files();

        $this->assertInstanceOf(FileResource::class, $resource);
        $this->assertSame($resource, $this->freelo->files(), 'Should return same instance');
    }

    public function testTaskLabelsReturnsTaskLabelResource(): void
    {
        $resource = $this->freelo->taskLabels();

        $this->assertInstanceOf(TaskLabelResource::class, $resource);
        $this->assertSame($resource, $this->freelo->taskLabels(), 'Should return same instance');
    }

    public function testCommentsReturnsCommentResource(): void
    {
        $resource = $this->freelo->comments();

        $this->assertInstanceOf(CommentResource::class, $resource);
        $this->assertSame($resource, $this->freelo->comments(), 'Should return same instance');
    }

    public function testSetAndGetApiUrl(): void
    {
        $customUrl = 'https://custom-api.example.com/v2';
        $result = $this->freelo->setApiUrl($customUrl);

        $this->assertSame($this->freelo, $result, 'Should return self for chaining');
        $this->assertSame($customUrl, $this->freelo->getApiUrl());
    }

    public function testSetAndGetUserAgent(): void
    {
        $customAgent = 'My-Custom-Agent/1.0';
        $result = $this->freelo->setUserAgent($customAgent);

        $this->assertSame($this->freelo, $result, 'Should return self for chaining');
        $this->assertSame($customAgent, $this->freelo->getUserAgent());
    }

    public function testGetCredentials(): void
    {
        $credentials = $this->freelo->getCredentials();

        $this->assertInstanceOf(ApiKeyCredentials::class, $credentials);
    }

    public function testGetClient(): void
    {
        $client = $this->freelo->getClient();

        $this->assertNotNull($client);
    }

    public function testGetCacheReturnsNullByDefault(): void
    {
        $this->assertNull($this->freelo->getCache());
    }
}
