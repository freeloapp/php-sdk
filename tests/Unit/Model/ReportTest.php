<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\Report;
use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => 123,
            'type' => 'weekly',
            'format' => 'pdf',
            'url' => 'https://example.com/reports/123.pdf',
            'date_add' => '2024-01-01T00:00:00Z',
        ];

        $report = Report::fromArray($data);

        $this->assertSame(123, $report->id);
        $this->assertSame('weekly', $report->type);
        $this->assertSame('pdf', $report->format);
        $this->assertSame('https://example.com/reports/123.pdf', $report->url);
        $this->assertSame('2024-01-01T00:00:00Z', $report->dateAdd);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'id' => 456,
            'type' => 'monthly',
        ];

        $report = Report::fromArray($data);

        $this->assertSame(456, $report->id);
        $this->assertSame('monthly', $report->type);
        $this->assertNull($report->format);
        $this->assertNull($report->url);
    }

    public function testToArray(): void
    {
        $data = ['id' => 789, 'type' => 'daily'];
        $report = Report::fromArray($data);

        $this->assertSame($data, $report->toArray());
    }
}
