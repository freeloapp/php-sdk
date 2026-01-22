<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Webhook;

use Freelo\Sdk\Exception\WebhookException;
use Freelo\Sdk\Webhook\Event\CommentAddedEvent;
use Freelo\Sdk\Webhook\Event\ProjectUpdatedEvent;
use Freelo\Sdk\Webhook\Event\TaskCreatedEvent;
use Freelo\Sdk\Webhook\Event\TaskUpdatedEvent;
use Freelo\Sdk\Webhook\WebhookHandler;
use PHPUnit\Framework\TestCase;

class WebhookHandlerTest extends TestCase
{
    public function testHandleTaskCreatedEvent(): void
    {
        $payload = json_encode([
            'event' => 'task.created',
            'data' => [
                'id' => '123',
                'name' => 'New Task',
                'project_id' => 'project-1',
                'creator_id' => 'user-1',
                'created_at' => '2025-12-28T10:00:00Z',
            ],
        ]);

        $handler = new WebhookHandler();
        $event = $handler->handle($payload);

        $this->assertInstanceOf(TaskCreatedEvent::class, $event);
        $this->assertEquals('task.created', $event->getType());
        $this->assertEquals('123', $event->getTaskId());
        $this->assertEquals('New Task', $event->getTaskName());
        $this->assertEquals('project-1', $event->getProjectId());
    }

    public function testHandleTaskUpdatedEvent(): void
    {
        $payload = json_encode([
            'event' => 'task.updated',
            'data' => [
                'id' => '123',
                'name' => 'Updated Task',
                'project_id' => 'project-1',
                'updated_fields' => ['name', 'status'],
                'updater_id' => 'user-1',
                'updated_at' => '2025-12-28T10:00:00Z',
            ],
        ]);

        $handler = new WebhookHandler();
        $event = $handler->handle($payload);

        $this->assertInstanceOf(TaskUpdatedEvent::class, $event);
        $this->assertEquals('task.updated', $event->getType());
        $this->assertEquals('123', $event->getTaskId());
        $this->assertEquals(['name', 'status'], $event->getUpdatedFields());
    }

    public function testHandleCommentAddedEvent(): void
    {
        $payload = json_encode([
            'event' => 'comment.added',
            'data' => [
                'id' => 'comment-1',
                'content' => 'This is a comment',
                'task_id' => '123',
                'project_id' => 'project-1',
                'author_id' => 'user-1',
                'created_at' => '2025-12-28T10:00:00Z',
            ],
        ]);

        $handler = new WebhookHandler();
        $event = $handler->handle($payload);

        $this->assertInstanceOf(CommentAddedEvent::class, $event);
        $this->assertEquals('comment.added', $event->getType());
        $this->assertEquals('comment-1', $event->getCommentId());
        $this->assertEquals('This is a comment', $event->getContent());
    }

    public function testHandleProjectUpdatedEvent(): void
    {
        $payload = json_encode([
            'event' => 'project.updated',
            'data' => [
                'id' => 'project-1',
                'name' => 'Updated Project',
                'updated_fields' => ['name', 'description'],
                'updater_id' => 'user-1',
                'updated_at' => '2025-12-28T10:00:00Z',
            ],
        ]);

        $handler = new WebhookHandler();
        $event = $handler->handle($payload);

        $this->assertInstanceOf(ProjectUpdatedEvent::class, $event);
        $this->assertEquals('project.updated', $event->getType());
        $this->assertEquals('project-1', $event->getProjectId());
        $this->assertEquals('Updated Project', $event->getProjectName());
    }

    public function testHandleWithSignatureVerification(): void
    {
        $secret = 'my-webhook-secret';
        $payload = json_encode([
            'event' => 'task.created',
            'data' => ['id' => '123'],
        ]);
        $signature = hash_hmac('sha256', $payload, $secret);

        $handler = new WebhookHandler($secret);
        $event = $handler->handle($payload, $signature);

        $this->assertInstanceOf(TaskCreatedEvent::class, $event);
    }

    public function testHandleThrowsExceptionOnInvalidSignature(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage('Invalid webhook signature');

        $secret = 'my-webhook-secret';
        $payload = json_encode([
            'event' => 'task.created',
            'data' => ['id' => '123'],
        ]);
        $invalidSignature = 'invalid-signature';

        $handler = new WebhookHandler($secret);
        $handler->handle($payload, $invalidSignature);
    }

    public function testHandleThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage('Invalid JSON payload');

        $handler = new WebhookHandler();
        $handler->handle('invalid json');
    }

    public function testHandleThrowsExceptionOnMissingEventField(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage('Missing "event" field');

        $payload = json_encode([
            'data' => ['id' => '123'],
        ]);

        $handler = new WebhookHandler();
        $handler->handle($payload);
    }

    public function testHandleThrowsExceptionOnUnknownEventType(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage('Unknown event type');

        $payload = json_encode([
            'event' => 'unknown.event',
            'data' => ['id' => '123'],
        ]);

        $handler = new WebhookHandler();
        $handler->handle($payload);
    }

    public function testHandleRequestWithHeaders(): void
    {
        $secret = 'my-webhook-secret';
        $payload = json_encode([
            'event' => 'task.created',
            'data' => ['id' => '123'],
        ]);
        $signature = hash_hmac('sha256', $payload, $secret);

        $headers = [
            'X-Freelo-Signature' => $signature,
            'Content-Type' => 'application/json',
        ];

        $event = WebhookHandler::handleRequest($payload, $headers, $secret);

        $this->assertInstanceOf(TaskCreatedEvent::class, $event);
    }
}
