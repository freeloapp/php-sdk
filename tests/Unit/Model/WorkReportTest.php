<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Currency;
use Freelo\Sdk\Model\Tasklist;
use Freelo\Sdk\Model\User;
use Freelo\Sdk\Model\WorkReport;
use PHPUnit\Framework\TestCase;

class WorkReportTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'minutes' => 120,
            'note' => 'Worked on feature implementation',
            'date_add' => '2024-01-01T10:00:00Z',
            'date_edited_at' => '2024-01-02T10:00:00Z',
            'date_reported' => '2024-01-01',
            'author' => [
                'id' => 456,
                'fullname' => 'John Doe',
            ],
            'worker' => [
                'id' => 789,
                'fullname' => 'Jane Smith',
            ],
            'cost' => [
                'amount' => '50000',
                'currency' => 'CZK',
            ],
            'task' => ['id' => 111, 'name' => 'Test Task'],
            'project' => ['id' => 222, 'name' => 'Test Project'],
        ];

        $report = WorkReport::fromArray($data);

        $this->assertSame(123, $report->id);
        $this->assertSame(120, $report->minutes);
        $this->assertSame('Worked on feature implementation', $report->note);
        $this->assertSame('2024-01-01T10:00:00Z', $report->dateAdd);
        $this->assertSame('2024-01-02T10:00:00Z', $report->dateEditedAt);
        $this->assertSame('2024-01-01', $report->dateReported);
        $this->assertInstanceOf(User::class, $report->author);
        $this->assertSame('John Doe', $report->author->fullname);
        $this->assertInstanceOf(User::class, $report->worker);
        $this->assertSame('Jane Smith', $report->worker->fullname);
        $this->assertInstanceOf(Currency::class, $report->cost);
        $this->assertSame('50000', $report->cost->amount);
        $this->assertSame(111, $report->getTaskId());
        $this->assertSame('Test Task', $report->getTaskName());
        $this->assertSame(222, $report->getProjectId());
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
        ];

        $report = WorkReport::fromArray($data);

        $this->assertSame(456, $report->id);
        $this->assertNull($report->minutes);
        $this->assertNull($report->note);
        $this->assertNull($report->author);
        $this->assertNull($report->cost);
        $this->assertNull($report->getTaskId());
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $report = WorkReport::fromArray($data);

        $this->assertSame(0, $report->id);
        $this->assertNull($report->minutes);
    }

    public function testGetHours(): void
    {
        $report = WorkReport::fromArray(['id' => 1, 'minutes' => 120]);

        $this->assertSame(2.0, $report->getHours());
    }

    public function testGetHoursWithFractionalMinutes(): void
    {
        $report = WorkReport::fromArray(['id' => 1, 'minutes' => 90]);

        $this->assertSame(1.5, $report->getHours());
    }

    public function testGetHoursWithNullMinutes(): void
    {
        $report = WorkReport::fromArray(['id' => 1]);

        $this->assertSame(0.0, $report->getHours());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 789,
            'minutes' => 60,
            'note' => 'Test work',
        ];

        $report = WorkReport::fromArray($data);
        $result = $report->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $author = User::fromArray(['id' => 100, 'fullname' => 'Author']);
        $worker = User::fromArray(['id' => 101, 'fullname' => 'Worker']);
        $cost = Currency::fromArray(['amount' => '25000', 'currency' => 'EUR']);
        $tasklist = Tasklist::fromArray(['id' => 300, 'name' => 'Test Tasklist']);

        $report = new WorkReport(
            id: 999,
            minutes: 180,
            note: 'Direct construction test',
            dateAdd: '2024-01-01T00:00:00Z',
            dateEditedAt: '2024-01-02T00:00:00Z',
            dateReported: '2024-01-01',
            author: $author,
            worker: $worker,
            cost: $cost,
            task: ['id' => 500, 'name' => 'Task Name'],
            tasklist: $tasklist,
            project: ['id' => 600, 'name' => 'Project Name'],
            data: ['custom' => 'data'],
        );

        $this->assertSame(999, $report->id);
        $this->assertSame(180, $report->minutes);
        $this->assertSame(3.0, $report->getHours());
        $this->assertSame($author, $report->author);
        $this->assertSame($cost, $report->cost);
        $this->assertSame(500, $report->getTaskId());
    }
}
