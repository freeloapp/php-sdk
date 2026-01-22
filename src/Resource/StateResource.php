<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\State;

/**
 * State resource manager
 *
 * Handles all state-related API operations.
 */
class StateResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'states';
    }

    /**
     * Get all available states
     *
     * @return State[]
     * @throws ApiException
     */
    public function list(): array
    {
        $response = $this->client->get('states');
        $data = $this->parser->parseSingle($response);

        return array_map(
            fn(array $item) => State::fromArray($item),
            $data['states'] ?? []
        );
    }
}
