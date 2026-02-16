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
use Freelo\Sdk\Resource\UserResource;
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

    public function testLazyInitializationWithoutCredentials(): void
    {
        $freelo = new Freelo();

        $this->assertInstanceOf(Freelo::class, $freelo);
    }

    public function testSetCredentials(): void
    {
        $freelo = new Freelo();
        $credentials = new ApiKeyCredentials('test-api-key', 'test@example.com');

        $result = $freelo->setCredentials($credentials);

        $this->assertSame($credentials, $freelo->getCredentials());
        $this->assertSame($freelo, $result, 'Should return self for chaining');
    }

    public function testGetCredentialsReturnsNullWithoutCredentials(): void
    {
        $freelo = new Freelo();

        $this->assertNull($freelo->getCredentials());
    }

    public function testWithCredentialsReturnsNewInstance(): void
    {
        $originalCredentials = new ApiKeyCredentials('original-key', 'original@example.com');
        $newCredentials = new ApiKeyCredentials('new-key', 'new@example.com');

        $original = new Freelo($originalCredentials);
        $derived = $original->withCredentials($newCredentials);

        $this->assertInstanceOf(Freelo::class, $derived);
        $this->assertNotSame($original, $derived, 'Should return a new instance');
        $this->assertSame($originalCredentials, $original->getCredentials(), 'Original should be unchanged');
        $this->assertSame($newCredentials, $derived->getCredentials());
    }

    public function testWithCredentialsInheritsConfiguration(): void
    {
        $customUrl = 'https://custom-api.example.com/v2';
        $customAgent = 'My-Custom-Agent/1.0';

        $freelo = new Freelo(new ApiKeyCredentials('test-api-key', 'test@example.com'));
        $freelo->setApiUrl($customUrl);
        $freelo->setUserAgent($customAgent);

        $derived = $freelo->withCredentials(new ApiKeyCredentials('other-key', 'other@example.com'));

        $this->assertSame($customUrl, $derived->getApiUrl());
        $this->assertSame($customAgent, $derived->getUserAgent());
    }

    public function testWithCredentialsHasResourceAccessors(): void
    {
        $derived = $this->freelo->withCredentials(new ApiKeyCredentials('other-key', 'other@example.com'));

        $this->assertInstanceOf(ProjectResource::class, $derived->projects());
        $this->assertInstanceOf(TaskResource::class, $derived->tasks());
        $this->assertInstanceOf(UserResource::class, $derived->users());
    }

    public function testConstructorWithUserAgent(): void
    {
        $freelo = new Freelo(
            new ApiKeyCredentials('test-api-key', 'test@example.com'),
            userAgent: 'MyApp/1.0',
        );

        $this->assertSame('MyApp/1.0', $freelo->getUserAgent());
    }

    public function testSetCredentialsWithUserAgent(): void
    {
        $freelo = new Freelo();
        $credentials = new ApiKeyCredentials('test-api-key', 'test@example.com');

        $freelo->setCredentials($credentials, 'MyApp/2.0');

        $this->assertSame($credentials, $freelo->getCredentials());
        $this->assertSame('MyApp/2.0', $freelo->getUserAgent());
    }

    public function testWithCredentialsWithUserAgent(): void
    {
        $derived = $this->freelo->withCredentials(
            new ApiKeyCredentials('other-key', 'other@example.com'),
            'DerivedApp/1.0',
        );

        $this->assertSame('DerivedApp/1.0', $derived->getUserAgent());
    }

    public function testWithCredentialsInheritsUserAgentWhenNotOverridden(): void
    {
        $this->freelo->setUserAgent('ParentApp/1.0');

        $derived = $this->freelo->withCredentials(
            new ApiKeyCredentials('other-key', 'other@example.com'),
        );

        $this->assertSame('ParentApp/1.0', $derived->getUserAgent());
    }

    public function testCallMethodExists(): void
    {
        $this->assertTrue(method_exists($this->freelo, 'call'));
    }

    public function testCallIsAvailableOnWithCredentialsInstances(): void
    {
        $derived = $this->freelo->withCredentials(new ApiKeyCredentials('other-key', 'other@example.com'));

        $this->assertTrue(method_exists($derived, 'call'));
    }
}
