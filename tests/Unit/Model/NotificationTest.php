<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Notification;
use Freelo\Sdk\Model\Project;
use Freelo\Sdk\Model\Tasklist;
use Freelo\Sdk\Model\User;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'type' => 'task_assigned',
            'date_action' => '2024-01-01T10:00:00Z',
            'author' => [
                'id' => 456,
                'fullname' => 'John Doe',
            ],
            'who' => [
                'id' => 789,
                'fullname' => 'Jane Smith',
            ],
            'is_unread' => true,
            'is_new' => true,
            'task' => ['id' => 111, 'name' => 'Test Task'],
            'project' => ['id' => 222, 'name' => 'Test Project'],
            'more_comments' => false,
            'more_users' => [
                ['id' => 333, 'fullname' => 'Bob Wilson'],
            ],
        ];

        $notification = Notification::fromArray($data);

        $this->assertSame(123, $notification->id);
        $this->assertSame('task_assigned', $notification->type);
        $this->assertSame('2024-01-01T10:00:00Z', $notification->dateAction);
        $this->assertInstanceOf(User::class, $notification->author);
        $this->assertSame('John Doe', $notification->author->fullname);
        $this->assertInstanceOf(User::class, $notification->who);
        $this->assertTrue($notification->isUnread);
        $this->assertTrue($notification->isNew);
        $this->assertSame(111, $notification->getTaskId());
        $this->assertSame('Test Task', $notification->getTaskName());
        $this->assertInstanceOf(Project::class, $notification->project);
        $this->assertFalse($notification->moreComments);
        $this->assertCount(1, $notification->moreUsers);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'type' => 'comment_mention',
        ];

        $notification = Notification::fromArray($data);

        $this->assertSame(456, $notification->id);
        $this->assertSame('comment_mention', $notification->type);
        $this->assertNull($notification->dateAction);
        $this->assertNull($notification->isUnread);
        $this->assertNull($notification->author);
        $this->assertNull($notification->getTaskId());
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $notification = Notification::fromArray($data);

        $this->assertSame(0, $notification->id);
        $this->assertSame('', $notification->type);
    }

    public function testIsUnreadMethod(): void
    {
        $unreadNotification = Notification::fromArray(['id' => 100, 'type' => 'task', 'is_unread' => true]);
        $readNotification = Notification::fromArray(['id' => 101, 'type' => 'task', 'is_unread' => false]);

        $this->assertTrue($unreadNotification->isUnread());
        $this->assertFalse($readNotification->isUnread());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'type' => 'project_invite',
        ];

        $notification = Notification::fromArray($data);
        $result = $notification->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $author = User::fromArray(['id' => 100, 'fullname' => 'Author']);
        $project = Project::fromArray(['id' => 200, 'name' => 'Test Project']);
        $tasklist = Tasklist::fromArray(['id' => 300, 'name' => 'Test Tasklist']);

        $notification = new Notification(
            id: 999,
            type: 'custom_type',
            dateAction: '2024-01-01T00:00:00Z',
            author: $author,
            who: null,
            isUnread: true,
            isNew: false,
            task: ['id' => 500, 'name' => 'Task Name'],
            tasklist: $tasklist,
            project: $project,
            comment: null,
            document: null,
            file: null,
            moreComments: true,
            moreUsers: [],
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $notification->id);
        $this->assertSame('custom_type', $notification->type);
        $this->assertTrue($notification->isUnread);
        $this->assertSame($author, $notification->author);
        $this->assertSame(500, $notification->getTaskId());
    }
}
