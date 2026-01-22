<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Unit\Model;

use Freelo\Sdk\Model\TaskLabel;
use PHPUnit\Framework\TestCase;

class TaskLabelTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Urgent',
            'color' => '#ff0000',
        ];

        $label = TaskLabel::fromArray($data);

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $label->uuid);
        $this->assertSame('Urgent', $label->name);
        $this->assertSame('#ff0000', $label->color);
        $this->assertSame($data, $label->data);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'uuid' => '550e8400-e29b-41d4-a716-446655440001',
            'name' => 'Bug',
        ];

        $label = TaskLabel::fromArray($data);

        $this->assertSame('550e8400-e29b-41d4-a716-446655440001', $label->uuid);
        $this->assertSame('Bug', $label->name);
        $this->assertNull($label->color);
    }

    public function testFromArrayWithEmptyData(): void
    {
        $data = [];

        $label = TaskLabel::fromArray($data);

        $this->assertSame('', $label->uuid);
        $this->assertSame('', $label->name);
        $this->assertNull($label->color);
    }

    public function testToArray(): void
    {
        $data = [
            'uuid' => '550e8400-e29b-41d4-a716-446655440002',
            'name' => 'Feature',
            'color' => '#00ff00',
        ];

        $label = TaskLabel::fromArray($data);
        $result = $label->toArray();

        $this->assertSame($data, $result);
    }

    public function testConstructorWithAllParameters(): void
    {
        $label = new TaskLabel(
            uuid: '550e8400-e29b-41d4-a716-446655440003',
            name: 'Direct Construction',
            color: '#0000ff',
            data: ['custom' => 'data'],
        );

        $this->assertSame('550e8400-e29b-41d4-a716-446655440003', $label->uuid);
        $this->assertSame('Direct Construction', $label->name);
        $this->assertSame('#0000ff', $label->color);
    }
}
