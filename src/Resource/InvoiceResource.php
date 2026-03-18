<?php

declare(strict_types=1);

namespace Freelo\Sdk\Resource;

use Freelo\Sdk\Exception\ApiException;
use Freelo\Sdk\Http\PaginatedResult;
use Freelo\Sdk\Model\IssuedInvoice;
use Freelo\Sdk\Model\WorkReportExtended;

/**
 * Invoice resource manager
 *
 * Handles all invoice-related API operations.
 */
class InvoiceResource extends AbstractResource
{
    protected function getEndpoint(): string
    {
        return 'issued-invoices';
    }

    protected function getSingleEndpoint(): string
    {
        return 'issued-invoice';
    }

    /**
     * Get issued invoices - paginated
     *
     * @param array<string, mixed> $filters
     * @return PaginatedResult<IssuedInvoice>
     * @throws ApiException
     */
    public function list(array $filters = []): PaginatedResult
    {
        $response = $this->client->get('issued-invoices', $filters);

        return $this->parser->parsePaginated($response, IssuedInvoice::class);
    }

    /**
     * Get invoice detail
     *
     * @throws ApiException
     */
    public function get(int $invoiceId): IssuedInvoice
    {
        $response = $this->client->get("issued-invoice/{$invoiceId}");
        $data = $this->parser->parseSingle($response);

        return IssuedInvoice::fromArray($data);
    }

    /**
     * Get invoice reports as JSON
     *
     * @return WorkReportExtended[]
     * @throws ApiException
     */
    public function getReportsJson(int $invoiceId): array
    {
        $response = $this->client->get("issued-invoice/{$invoiceId}/reports-json");
        $data = $this->parser->parseCollection($response);

        return array_map(fn(array $item) => WorkReportExtended::fromArray($item), $data);
    }

    /**
     * Download invoice reports as CSV
     *
     * @throws ApiException
     */
    public function downloadReports(int $invoiceId): string
    {
        $response = $this->client->get("issued-invoice/{$invoiceId}/reports");

        return $response->getBody();
    }

    /**
     * Mark invoice as invoiced
     *
     * @throws ApiException
     */
    public function markAsInvoiced(int $invoiceId, string $url, string $subject): IssuedInvoice
    {
        $response = $this->client->post("issued-invoice/{$invoiceId}/mark-as-invoiced", [
            'url' => $url,
            'subject' => $subject,
        ]);
        $data = $this->parser->parseSingle($response);

        return IssuedInvoice::fromArray($data);
    }
}
