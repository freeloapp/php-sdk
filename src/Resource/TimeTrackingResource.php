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
     * @throws ApiException
     */
    public function edit(?int $taskId = null, ?string $note = null): string
    {
        $data = [];
        if ($taskId !== null) {
            $data['task_id'] = $taskId;
        }
        if ($note !== null) {
            $data['note'] = $note;
        }

        $response = $this->client->post('timetracking/edit', $data);
        $responseData = $this->parser->parseSingle($response);

        return (string) ($responseData['uuid'] ?? '');
    }
}
