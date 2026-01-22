<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Model\CustomField;

/**
 * Custom field resource manager
 *
 * Handles all custom field-related API operations.
 */
class CustomFieldResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'custom-field';
    }

    /**
     * Get all custom field types
     *
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function getTypes(): array
    {
        $response = $this->client->get('custom-field/get-types');

        return $this->parser->parseSingle($response);
    }

    /**
     * Create a custom field in a project
     *
     * @param array<string, mixed> $data
     * @throws ApiException
     */
    public function create(int $projectId, array $data): CustomField
    {
        $response = $this->client->post("custom-field/create/{$projectId}", $data);
        $responseData = $this->parser->parseSingle($response);

        return CustomField::fromArray($responseData);
    }

    /**
     * Rename a custom field
     *
     * @throws ApiException
     */
    public function rename(string $uuid, string $name): bool
    {
        $response = $this->client->post("custom-field/rename/{$uuid}", [
            'name' => $name,
        ]);

        return $this->parser->parseBoolean($response);
    }

    /**
     * Delete a custom field
     *
     * @throws ApiException
     */
    public function delete(string $uuid): bool
    {
        $response = $this->client->delete("custom-field/delete/{$uuid}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Restore a deleted custom field
     *
     * @throws ApiException
     */
    public function restore(string $uuid): bool
    {
        $response = $this->client->post("custom-field/restore/{$uuid}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Add or edit a custom field value
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function addOrEditValue(array $data): array
    {
        $response = $this->client->post('custom-field/add-or-edit-value', $data);

        return $this->parser->parseSingle($response);
    }

    /**
     * Add or edit an enum custom field value
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function addOrEditEnumValue(array $data): array
    {
        $response = $this->client->post('custom-field/add-or-edit-enum-value', $data);

        return $this->parser->parseSingle($response);
    }

    /**
     * Delete a custom field value
     *
     * @throws ApiException
     */
    public function deleteValue(string $uuid): bool
    {
        $response = $this->client->delete("custom-field/delete-value/{$uuid}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Get enum options for a custom field
     *
     * @return array<int, array<string, mixed>>
     * @throws ApiException
     */
    public function getEnumOptions(string $uuid): array
    {
        $response = $this->client->get("custom-field-enum/get-for-custom-field/{$uuid}");

        return $this->parser->parseCollection($response);
    }

    /**
     * Create an enum option for a custom field
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function createEnumOption(string $uuid, array $data): array
    {
        $response = $this->client->post("custom-field-enum/create/{$uuid}", $data);

        return $this->parser->parseSingle($response);
    }

    /**
     * Change an enum option
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function changeEnumOption(string $uuid, array $data): array
    {
        $response = $this->client->post("custom-field-enum/change/{$uuid}", $data);

        return $this->parser->parseSingle($response);
    }

    /**
     * Delete an enum option
     *
     * @throws ApiException
     */
    public function deleteEnumOption(string $uuid): bool
    {
        $response = $this->client->delete("custom-field-enum/delete/{$uuid}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Force delete an enum option (even if in use)
     *
     * @throws ApiException
     */
    public function forceDeleteEnumOption(string $uuid): bool
    {
        $response = $this->client->delete("custom-field-enum/force-delete/{$uuid}");

        return $this->parser->parseBoolean($response);
    }

    /**
     * Find custom fields by project
     *
     * @return CustomField[]
     * @throws ApiException
     */
    public function findByProject(int $projectId): array
    {
        $response = $this->client->get("custom-field/find-by-project/{$projectId}");
        $data = $this->parser->parseCollection($response);

        return array_map(fn(array $item) => CustomField::fromArray($item), $data);
    }
}
