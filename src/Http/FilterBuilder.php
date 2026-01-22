<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

/**
 * Fluent builder for API filter parameters
 *
 * Example usage:
 * ```php
 * use Freelo\Sdk\Http\FilterBuilder;
 *
 * $filters = FilterBuilder::create()
 *     ->page(0)
 *     ->orderBy('date_add', 'desc')
 *     ->stateIds([1]) // Active only
 *     ->createdInRange('2024-01-01', '2024-12-31')
 *     ->build();
 *
 * $projects = $freelo->projects()->getAll($filters);
 * ```
 */
class FilterBuilder
{
    /** @var array<string, mixed> */
    private array $filters = [];

    /**
     * Create a new FilterBuilder instance
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Set page number (0-indexed)
     */
    public function page(int $page): self
    {
        $this->filters['p'] = $page;
        return $this;
    }

    /**
     * Set ordering
     *
     * @param string $field Field to order by
     * @param string $direction Order direction ('asc' or 'desc')
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->filters['order_by'] = $field;
        $this->filters['order'] = $direction;
        return $this;
    }

    /**
     * Filter by project IDs
     *
     * @param int[] $ids
     */
    public function projectIds(array $ids): self
    {
        $this->filters['projects_ids'] = $ids;
        return $this;
    }

    /**
     * Filter by user IDs
     *
     * @param int[] $ids
     */
    public function userIds(array $ids): self
    {
        $this->filters['users_ids'] = $ids;
        return $this;
    }

    /**
     * Filter by task IDs
     *
     * @param int[] $ids
     */
    public function taskIds(array $ids): self
    {
        $this->filters['tasks_ids'] = $ids;
        return $this;
    }

    /**
     * Filter by tasklist IDs
     *
     * @param int[] $ids
     */
    public function tasklistIds(array $ids): self
    {
        $this->filters['tasklists_ids'] = $ids;
        return $this;
    }

    /**
     * Filter by state IDs (1=active, 2=archived, 3=template)
     *
     * @param int[] $ids
     */
    public function stateIds(array $ids): self
    {
        $this->filters['states_ids'] = $ids;
        return $this;
    }

    /**
     * Filter by tags
     *
     * @param string[] $tags
     */
    public function tags(array $tags): self
    {
        $this->filters['tags'] = $tags;
        return $this;
    }

    /**
     * Filter by date range (created)
     *
     * @param string|null $from Start date (Y-m-d format)
     * @param string|null $to End date (Y-m-d format)
     */
    public function createdInRange(?string $from = null, ?string $to = null): self
    {
        if ($from !== null) {
            $this->filters['created_in_range']['date_from'] = $from;
        }
        if ($to !== null) {
            $this->filters['created_in_range']['date_to'] = $to;
        }
        return $this;
    }

    /**
     * Filter by due date range
     *
     * @param string|null $from Start date (Y-m-d format)
     * @param string|null $to End date (Y-m-d format)
     */
    public function dueDateRange(?string $from = null, ?string $to = null): self
    {
        if ($from !== null) {
            $this->filters['due_date_range']['date_from'] = $from;
        }
        if ($to !== null) {
            $this->filters['due_date_range']['date_to'] = $to;
        }
        return $this;
    }

    /**
     * Filter by task state ID
     */
    public function taskState(int $stateId): self
    {
        $this->filters['state_id'] = $stateId;
        return $this;
    }

    /**
     * Filter by worker ID
     */
    public function workerId(int $id): self
    {
        $this->filters['worker_id'] = $id;
        return $this;
    }

    /**
     * Search query
     */
    public function search(string $query): self
    {
        $this->filters['search_query'] = $query;
        return $this;
    }

    /**
     * Filter tasks with label
     */
    public function withLabel(string $label): self
    {
        $this->filters['with_label'] = $label;
        return $this;
    }

    /**
     * Filter tasks without label
     */
    public function withoutLabel(string $label): self
    {
        $this->filters['without_label'] = $label;
        return $this;
    }

    /**
     * Only tasks with no due date
     */
    public function noDueDate(bool $value = true): self
    {
        $this->filters['no_due_date'] = $value;
        return $this;
    }

    /**
     * Only unread notifications
     */
    public function onlyUnread(bool $value = true): self
    {
        $this->filters['only_unread'] = $value;
        return $this;
    }

    /**
     * Filter tasks finished after their due date
     */
    public function finishedOverdue(bool $value = true): self
    {
        $this->filters['finished_overdue'] = $value;
        return $this;
    }

    /**
     * Filter by finished date range
     *
     * @param string|null $from Start date (Y-m-d format)
     * @param string|null $to End date (Y-m-d format)
     */
    public function finishedDateRange(?string $from = null, ?string $to = null): self
    {
        if ($from !== null) {
            $this->filters['finished_date_range']['date_from'] = $from;
        }
        if ($to !== null) {
            $this->filters['finished_date_range']['date_to'] = $to;
        }
        return $this;
    }

    /**
     * Filter work reports by task label UUIDs
     *
     * @param string[] $uuids
     */
    public function tasksLabels(array $uuids): self
    {
        $this->filters['tasks_labels'] = $uuids;
        return $this;
    }

    /**
     * Filter by date reported range (for work reports)
     *
     * @param string|null $from Start date (Y-m-d format)
     * @param string|null $to End date (Y-m-d format)
     */
    public function dateReportedRange(?string $from = null, ?string $to = null): self
    {
        if ($from !== null) {
            $this->filters['date_reported_range']['date_from'] = $from;
        }
        if ($to !== null) {
            $this->filters['date_reported_range']['date_to'] = $to;
        }
        return $this;
    }

    /**
     * Filter by date added range (for work reports)
     *
     * @param string|null $from Start date (Y-m-d format)
     * @param string|null $to End date (Y-m-d format)
     */
    public function dateAddRange(?string $from = null, ?string $to = null): self
    {
        if ($from !== null) {
            $this->filters['date_add_range']['date_from'] = $from;
        }
        if ($to !== null) {
            $this->filters['date_add_range']['date_to'] = $to;
        }
        return $this;
    }

    /**
     * Filter by date edited from (for work reports)
     *
     * @param string $date Date (Y-m-d format)
     */
    public function dateEditedFrom(string $date): self
    {
        $this->filters['date_edited_from'] = $date;
        return $this;
    }

    /**
     * Filter notifications by team UUIDs
     *
     * @param string[] $uuids
     */
    public function teamsUuids(array $uuids): self
    {
        $this->filters['teams_uuids'] = $uuids;
        return $this;
    }

    /**
     * Filter by notification types
     *
     * @param string[] $types
     */
    public function notificationTypes(array $types): self
    {
        $this->filters['notification_types'] = $types;
        return $this;
    }

    /**
     * Filter events by type
     *
     * @param string[] $types
     */
    public function eventTypes(array $types): self
    {
        $this->filters['events_types'] = $types;
        return $this;
    }

    /**
     * Filter by general date range (for events)
     *
     * @param string|null $from Start date (Y-m-d format)
     * @param string|null $to End date (Y-m-d format)
     */
    public function dateRange(?string $from = null, ?string $to = null): self
    {
        if ($from !== null) {
            $this->filters['date_range']['date_from'] = $from;
        }
        if ($to !== null) {
            $this->filters['date_range']['date_to'] = $to;
        }
        return $this;
    }

    /**
     * Set order direction only (asc/desc)
     */
    public function order(string $direction): self
    {
        $this->filters['order'] = $direction;
        return $this;
    }

    /**
     * Add custom filter
     *
     * @param string $key Filter key
     * @param mixed $value Filter value
     */
    public function custom(string $key, mixed $value): self
    {
        $this->filters[$key] = $value;
        return $this;
    }

    /**
     * Build the filter array
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return $this->filters;
    }

    /**
     * Merge with existing array
     *
     * @param array<string, mixed> $existing Existing parameters to merge with
     * @return array<string, mixed>
     */
    public function mergeWith(array $existing): array
    {
        return array_merge($existing, $this->filters);
    }

    /**
     * Reset the builder to empty state
     */
    public function reset(): self
    {
        $this->filters = [];
        return $this;
    }

    /**
     * Check if any filters have been set
     */
    public function isEmpty(): bool
    {
        return empty($this->filters);
    }

    /**
     * Check if a specific filter is set
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->filters);
    }

    /**
     * Get a specific filter value
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->filters[$key] ?? $default;
    }

    /**
     * Remove a specific filter
     */
    public function remove(string $key): self
    {
        unset($this->filters[$key]);
        return $this;
    }
}
