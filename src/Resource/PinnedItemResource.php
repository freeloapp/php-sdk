<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\PinnedItem;

/**
 * Pinned item resource manager
 *
 * Handles all pinned item-related API operations.
 */
class PinnedItemResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'pinned-items';
    }

    protected function getSingleEndpoint(): string
    {
        return 'pinned-item';
    }

    /**
     * Get pinned items in a project
     *
     * @return PinnedItem[]
     * @throws ApiException
     */
    public function list(int $projectId): array
    {
        $response = $this->client->get("project/{$projectId}/pinned-items");
        $data = $this->parser->parseCollection($response);

        return array_map(fn(array $item) => PinnedItem::fromArray($item), $data);
    }

    /**
     * Pin an item to a project
     *
     * @throws ApiException
     */
    public function create(int $projectId, string $link, ?string $title = null): PinnedItem
    {
        $data = ['link' => $link];
        if ($title !== null) {
            $data['title'] = $title;
        }

        $response = $this->client->post("project/{$projectId}/pinned-items", $data);
        $responseData = $this->parser->parseSingle($response);

        return PinnedItem::fromArray($responseData);
    }

    /**
     * Delete a pinned item
     *
     * @throws ApiException
     */
    public function delete(int $pinnedItemId): bool
    {
        $response = $this->client->delete("pinned-item/{$pinnedItemId}");

        return $this->parser->parseBoolean($response);
    }
}
