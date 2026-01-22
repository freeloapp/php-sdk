<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\CustomField;
use PHPUnit\Framework\TestCase;

class CustomFieldTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'uuid' => 'abc-123-def',
            'name' => 'Priority Level',
            'custom_fields_types_uuid' => 'type-uuid-123',
            'project_id' => 456,
            'author_id' => 789,
            'date_add' => '2024-01-01T00:00:00Z',
            'priority' => 1,
            'value' => 'high',
        ];

        $field = CustomField::fromArray($data);

        $this->assertSame('abc-123-def', $field->uuid);
        $this->assertSame('Priority Level', $field->name);
        $this->assertSame('type-uuid-123', $field->typeUuid);
        $this->assertSame(456, $field->projectId);
        $this->assertSame(789, $field->authorId);
        $this->assertSame('2024-01-01T00:00:00Z', $field->dateAdd);
        $this->assertSame(1, $field->priority);
        $this->assertSame('high', $field->value);
        $this->assertSame($data, $field->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'uuid' => 'uuid-456',
            'name' => 'Description',
        ];

        $field = CustomField::fromArray($data);

        $this->assertSame('uuid-456', $field->uuid);
        $this->assertSame('Description', $field->name);
        $this->assertNull($field->typeUuid);
        $this->assertNull($field->value);
        $this->assertNull($field->projectId);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $field = CustomField::fromArray($data);

        $this->assertSame('', $field->uuid);
        $this->assertSame('', $field->name);
    }

    public function testFromArrayWithNumericValue(): void
    {
        $data = [
            'uuid' => 'uuid-789',
            'name' => 'Estimate Hours',
            'value' => 40,
        ];

        $field = CustomField::fromArray($data);

        $this->assertSame(40, $field->value);
    }

    public function testFromArrayWithArrayValue(): void
    {
        $data = [
            'uuid' => 'uuid-101',
            'name' => 'Tags',
            'value' => ['frontend', 'backend', 'urgent'],
        ];

        $field = CustomField::fromArray($data);

        $this->assertSame(['frontend', 'backend', 'urgent'], $field->value);
    }

    public function testFromArrayWithBooleanValue(): void
    {
        $data = [
            'uuid' => 'uuid-102',
            'name' => 'Is Approved',
            'value' => true,
        ];

        $field = CustomField::fromArray($data);

        $this->assertTrue($field->value);
    }

    public function testToArray(): void
    {
        $data = [
            'uuid' => 'uuid-999',
            'name' => 'Test Field',
            'value' => 'test value',
        ];

        $field = CustomField::fromArray($data);
        $result = $field->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $field = new CustomField(
            uuid: 'uuid-999',
            name: 'Direct Construction',
            typeUuid: 'type-uuid',
            projectId: 100,
            authorId: 200,
            dateAdd: '2024-01-01T00:00:00Z',
            priority: 5,
            value: '2024-01-01',
            data: ['custom' => 'data'],
        );

        $this->assertSame('uuid-999', $field->uuid);
        $this->assertSame('Direct Construction', $field->name);
        $this->assertSame('type-uuid', $field->typeUuid);
        $this->assertSame('2024-01-01', $field->value);
        $this->assertSame(5, $field->priority);
    }
}
