<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Http;

use Freelo\Sdk\Http\FilterBuilder;
use PHPUnit\Framework\TestCase;

class FilterBuilderTest extends TestCase
{
    public function testCreateReturnsNewInstance(): void
    {
        $builder = FilterBuilder::create();

        $this->assertInstanceOf(FilterBuilder::class, $builder);
    }

    public function testPage(): void
    {
        $filters = FilterBuilder::create()
            ->page(5)
            ->build();

        $this->assertSame(['p' => 5], $filters);
    }

    public function testOrderBy(): void
    {
        $filters = FilterBuilder::create()
            ->orderBy('date_add', 'desc')
            ->build();

        $this->assertSame([
            'order_by' => 'date_add',
            'order' => 'desc',
        ], $filters);
    }

    public function testOrderByDefaultsToAsc(): void
    {
        $filters = FilterBuilder::create()
            ->orderBy('name')
            ->build();

        $this->assertSame([
            'order_by' => 'name',
            'order' => 'asc',
        ], $filters);
    }

    public function testProjectIds(): void
    {
        $filters = FilterBuilder::create()
            ->projectIds([1, 2, 3])
            ->build();

        $this->assertSame(['projects_ids' => [1, 2, 3]], $filters);
    }

    public function testUserIds(): void
    {
        $filters = FilterBuilder::create()
            ->userIds([10, 20])
            ->build();

        $this->assertSame(['users_ids' => [10, 20]], $filters);
    }

    public function testTaskIds(): void
    {
        $filters = FilterBuilder::create()
            ->taskIds([100, 200])
            ->build();

        $this->assertSame(['tasks_ids' => [100, 200]], $filters);
    }

    public function testTasklistIds(): void
    {
        $filters = FilterBuilder::create()
            ->tasklistIds([5, 6])
            ->build();

        $this->assertSame(['tasklists_ids' => [5, 6]], $filters);
    }

    public function testStateIds(): void
    {
        $filters = FilterBuilder::create()
            ->stateIds([1, 2])
            ->build();

        $this->assertSame(['states_ids' => [1, 2]], $filters);
    }

    public function testTags(): void
    {
        $filters = FilterBuilder::create()
            ->tags(['urgent', 'bug'])
            ->build();

        $this->assertSame(['tags' => ['urgent', 'bug']], $filters);
    }

    public function testCreatedInRange(): void
    {
        $filters = FilterBuilder::create()
            ->createdInRange('2024-01-01', '2024-12-31')
            ->build();

        $this->assertSame([
            'created_in_range' => [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testCreatedInRangeOnlyFrom(): void
    {
        $filters = FilterBuilder::create()
            ->createdInRange('2024-01-01')
            ->build();

        $this->assertSame([
            'created_in_range' => [
                'date_from' => '2024-01-01',
            ],
        ], $filters);
    }

    public function testCreatedInRangeOnlyTo(): void
    {
        $filters = FilterBuilder::create()
            ->createdInRange(null, '2024-12-31')
            ->build();

        $this->assertSame([
            'created_in_range' => [
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testDueDateRange(): void
    {
        $filters = FilterBuilder::create()
            ->dueDateRange('2024-01-01', '2024-12-31')
            ->build();

        $this->assertSame([
            'due_date_range' => [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testTaskState(): void
    {
        $filters = FilterBuilder::create()
            ->taskState(1)
            ->build();

        $this->assertSame(['state_id' => 1], $filters);
    }

    public function testWorkerId(): void
    {
        $filters = FilterBuilder::create()
            ->workerId(42)
            ->build();

        $this->assertSame(['worker_id' => 42], $filters);
    }

    public function testSearch(): void
    {
        $filters = FilterBuilder::create()
            ->search('test query')
            ->build();

        $this->assertSame(['search_query' => 'test query'], $filters);
    }

    public function testWithLabel(): void
    {
        $filters = FilterBuilder::create()
            ->withLabel('important')
            ->build();

        $this->assertSame(['with_label' => 'important'], $filters);
    }

    public function testWithoutLabel(): void
    {
        $filters = FilterBuilder::create()
            ->withoutLabel('low-priority')
            ->build();

        $this->assertSame(['without_label' => 'low-priority'], $filters);
    }

    public function testNoDueDate(): void
    {
        $filters = FilterBuilder::create()
            ->noDueDate()
            ->build();

        $this->assertSame(['no_due_date' => true], $filters);
    }

    public function testNoDueDateFalse(): void
    {
        $filters = FilterBuilder::create()
            ->noDueDate(false)
            ->build();

        $this->assertSame(['no_due_date' => false], $filters);
    }

    public function testOnlyUnread(): void
    {
        $filters = FilterBuilder::create()
            ->onlyUnread()
            ->build();

        $this->assertSame(['only_unread' => true], $filters);
    }

    public function testCustom(): void
    {
        $filters = FilterBuilder::create()
            ->custom('custom_key', 'custom_value')
            ->build();

        $this->assertSame(['custom_key' => 'custom_value'], $filters);
    }

    public function testChaining(): void
    {
        $filters = FilterBuilder::create()
            ->page(0)
            ->orderBy('date_add', 'desc')
            ->stateIds([1])
            ->projectIds([10, 20])
            ->build();

        $this->assertSame([
            'p' => 0,
            'order_by' => 'date_add',
            'order' => 'desc',
            'states_ids' => [1],
            'projects_ids' => [10, 20],
        ], $filters);
    }

    public function testMergeWith(): void
    {
        $existing = ['existing_key' => 'existing_value'];

        $result = FilterBuilder::create()
            ->page(0)
            ->mergeWith($existing);

        $this->assertSame([
            'existing_key' => 'existing_value',
            'p' => 0,
        ], $result);
    }

    public function testMergeWithOverwrites(): void
    {
        $existing = ['p' => 5];

        $result = FilterBuilder::create()
            ->page(0)
            ->mergeWith($existing);

        // Builder values should overwrite existing
        $this->assertSame(['p' => 0], $result);
    }

    public function testReset(): void
    {
        $builder = FilterBuilder::create()
            ->page(0)
            ->stateIds([1]);

        $builder->reset();

        $this->assertTrue($builder->isEmpty());
        $this->assertSame([], $builder->build());
    }

    public function testIsEmpty(): void
    {
        $builder = FilterBuilder::create();
        $this->assertTrue($builder->isEmpty());

        $builder->page(0);
        $this->assertFalse($builder->isEmpty());
    }

    public function testHas(): void
    {
        $builder = FilterBuilder::create()->page(0);

        $this->assertTrue($builder->has('p'));
        $this->assertFalse($builder->has('nonexistent'));
    }

    public function testGet(): void
    {
        $builder = FilterBuilder::create()->page(5);

        $this->assertSame(5, $builder->get('p'));
        $this->assertNull($builder->get('nonexistent'));
        $this->assertSame('default', $builder->get('nonexistent', 'default'));
    }

    public function testRemove(): void
    {
        $builder = FilterBuilder::create()
            ->page(0)
            ->stateIds([1]);

        $builder->remove('p');

        $this->assertFalse($builder->has('p'));
        $this->assertTrue($builder->has('states_ids'));
        $this->assertSame(['states_ids' => [1]], $builder->build());
    }

    public function testFinishedOverdue(): void
    {
        $filters = FilterBuilder::create()
            ->finishedOverdue()
            ->build();

        $this->assertSame(['finished_overdue' => true], $filters);
    }

    public function testFinishedOverdueFalse(): void
    {
        $filters = FilterBuilder::create()
            ->finishedOverdue(false)
            ->build();

        $this->assertSame(['finished_overdue' => false], $filters);
    }

    public function testFinishedDateRange(): void
    {
        $filters = FilterBuilder::create()
            ->finishedDateRange('2024-01-01', '2024-12-31')
            ->build();

        $this->assertSame([
            'finished_date_range' => [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testTasksLabels(): void
    {
        $uuids = ['550e8400-e29b-41d4-a716-446655440000', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'];
        $filters = FilterBuilder::create()
            ->tasksLabels($uuids)
            ->build();

        $this->assertSame(['tasks_labels' => $uuids], $filters);
    }

    public function testDateReportedRange(): void
    {
        $filters = FilterBuilder::create()
            ->dateReportedRange('2024-01-01', '2024-12-31')
            ->build();

        $this->assertSame([
            'date_reported_range' => [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testDateAddRange(): void
    {
        $filters = FilterBuilder::create()
            ->dateAddRange('2024-01-01', '2024-12-31')
            ->build();

        $this->assertSame([
            'date_add_range' => [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testDateEditedFrom(): void
    {
        $filters = FilterBuilder::create()
            ->dateEditedFrom('2024-06-15')
            ->build();

        $this->assertSame(['date_edited_from' => '2024-06-15'], $filters);
    }

    public function testTeamsUuids(): void
    {
        $uuids = ['550e8400-e29b-41d4-a716-446655440000'];
        $filters = FilterBuilder::create()
            ->teamsUuids($uuids)
            ->build();

        $this->assertSame(['teams_uuids' => $uuids], $filters);
    }

    public function testNotificationTypes(): void
    {
        $types = ['task_created', 'comment_added'];
        $filters = FilterBuilder::create()
            ->notificationTypes($types)
            ->build();

        $this->assertSame(['notification_types' => $types], $filters);
    }

    public function testEventTypes(): void
    {
        $types = ['task_created', 'task_finished'];
        $filters = FilterBuilder::create()
            ->eventTypes($types)
            ->build();

        $this->assertSame(['events_types' => $types], $filters);
    }

    public function testDateRange(): void
    {
        $filters = FilterBuilder::create()
            ->dateRange('2024-01-01', '2024-12-31')
            ->build();

        $this->assertSame([
            'date_range' => [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ],
        ], $filters);
    }

    public function testOrder(): void
    {
        $filters = FilterBuilder::create()
            ->order('desc')
            ->build();

        $this->assertSame(['order' => 'desc'], $filters);
    }
}
