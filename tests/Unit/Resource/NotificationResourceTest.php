<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Resource;

use Freelo\Sdk\Http\FilterBuilder;
use Freelo\Sdk\Http\FreeloClient;
use Freelo\Sdk\Http\Response;
use Freelo\Sdk\Http\ResponseParser;
use Freelo\Sdk\Resource\NotificationResource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class NotificationResourceTest extends TestCase
{
    private FreeloClient $client;
    private NotificationResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(FreeloClient::class);

        $this->client->method('getResponseParser')
            ->willReturn(new ResponseParser());

        $this->resource = new NotificationResource($this->client);
    }

    public function testListSendsTheDocumentedFiltersUnderTheNamesTheApiReads(): void
    {
        $this->client->expects($this->once())
            ->method('get')
            ->with('all-notifications', [
                'order' => 'desc',
                'is_only_unread' => 1,
                'notifications_types' => ['comment_new'],
            ])
            ->willReturn($this->createListResponse());

        $this->resource->list([
            'order' => 'desc',
            'only_unread' => 1,
            'notification_types' => ['comment_new'],
        ]);
    }

    public function testListKeepsTheNamesTheApiReads(): void
    {
        $filters = FilterBuilder::create()
            ->onlyUnread()
            ->notificationTypes(['task_assigned'])
            ->build();

        $this->client->expects($this->once())
            ->method('get')
            ->with('all-notifications', [
                'is_only_unread' => true,
                'notifications_types' => ['task_assigned'],
            ])
            ->willReturn($this->createListResponse());

        $this->resource->list($filters);
    }

    public function testListPrefersTheNameTheApiReadsWhenBothAreGiven(): void
    {
        $this->client->expects($this->once())
            ->method('get')
            ->with('all-notifications', ['is_only_unread' => 0])
            ->willReturn($this->createListResponse());

        $this->resource->list(['only_unread' => 1, 'is_only_unread' => 0]);
    }

    private function createListResponse(): Response
    {
        $body = json_encode([
            'total' => 0,
            'count' => 0,
            'page' => 0,
            'per_page' => 100,
            'data' => ['notifications' => []],
        ], JSON_THROW_ON_ERROR);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);

        $psrResponse = $this->createMock(ResponseInterface::class);
        $psrResponse->method('getStatusCode')->willReturn(200);
        $psrResponse->method('getBody')->willReturn($stream);

        return new Response($psrResponse);
    }
}
