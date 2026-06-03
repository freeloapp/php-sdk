<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\WorkReport;

/**
 * Time tracking resource manager
 *
 * Handles time tracking operations (start/stop/edit timer).
 */
class TimeTrackingResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'timetracking';
    }

    /**
     * Get current time tracking status
     *
     * Returns null when no active tracking session (204 response),
     * or the active session data when tracking is running (200 response).
     *
     * @return array<string, mixed>|null
     * @throws ApiException
     */
    public function status(): ?array
    {
        $response = $this->client->get('timetracking/status');
        $response->throwIfError();

        if ($response->getStatusCode() === 204) {
            return null;
        }

        return $response->json();
    }

    /**
     * Start time tracking
     *
     * @throws ApiException
     */
    public function start(?int $taskId = null, ?string $note = null): string
    {
        $data = [];
        if ($taskId !== null) {
            $data['task_id'] = $taskId;
        }
        if ($note !== null) {
            $data['note'] = $note;
        }

        $response = $this->client->post('timetracking/start', $data);
        $responseData = $this->parser->parseSingle($response);

        return (string) ($responseData['uuid'] ?? '');
    }

    /**
     * Stop time tracking
     *
     * @throws ApiException
     */
    public function stop(): WorkReport
    {
        $response = $this->client->post('timetracking/stop');
        $data = $this->parser->parseSingle($response);

        return WorkReport::fromArray($data);
    }

    /**
     * Edit running time tracking
     *
     * All fields are partial - only provided values are updated.
     *
     * @param string|null $dateReported New start timestamp for the running session
     *   (ISO 8601, e.g. '2026-05-07T09:00:00+02:00'). Used to correct or backdate
     *   the timer's start time; elapsed duration on stop() is computed from this value.
     * @throws ApiException
     */
    public function edit(?int $taskId = null, ?string $note = null, ?string $dateReported = null): string
    {
        $data = [];
        if ($taskId !== null) {
            $data['task_id'] = $taskId;
        }
        if ($note !== null) {
            $data['note'] = $note;
        }
        if ($dateReported !== null) {
            $data['date_reported'] = $dateReported;
        }

        $response = $this->client->post('timetracking/edit', $data);
        $responseData = $this->parser->parseSingle($response);

        return (string) ($responseData['uuid'] ?? '');
    }
}
