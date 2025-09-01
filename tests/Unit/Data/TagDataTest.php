<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Data;

use KemokRepos\Larabrandly\Data\TagData;
use PHPUnit\Framework\TestCase;

class TagDataTest extends TestCase
{
    public function test_can_create_from_array_with_all_fields(): void
    {
        $data = [
            'id' => 'tag123',
            'name' => 'Marketing',
            'color' => '#ff0000',
            'createdAt' => '2023-01-01T12:00:00Z',
            'updatedAt' => '2023-01-02T12:00:00Z',
            'linksCount' => 42,
        ];

        $tagData = TagData::fromArray($data);

        $this->assertEquals('tag123', $tagData->id);
        $this->assertEquals('Marketing', $tagData->name);
        $this->assertEquals('#ff0000', $tagData->color);
        $this->assertInstanceOf(\DateTimeImmutable::class, $tagData->createdAt);
        $this->assertEquals('2023-01-01T12:00:00+00:00', $tagData->createdAt->format('c'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $tagData->updatedAt);
        $this->assertEquals('2023-01-02T12:00:00+00:00', $tagData->updatedAt->format('c'));
        $this->assertEquals(42, $tagData->linksCount);
    }

    public function test_can_create_from_array_with_minimal_fields(): void
    {
        $data = [
            'id' => 'tag123',
            'name' => 'Marketing',
        ];

        $tagData = TagData::fromArray($data);

        $this->assertEquals('tag123', $tagData->id);
        $this->assertEquals('Marketing', $tagData->name);
        $this->assertNull($tagData->color);
        $this->assertNull($tagData->createdAt);
        $this->assertNull($tagData->updatedAt);
        $this->assertNull($tagData->linksCount);
    }

    public function test_to_array_includes_only_non_null_values(): void
    {
        $tagData = new TagData(
            name: 'Marketing',
            color: '#ff0000'
        );

        $array = $tagData->toArray();

        $this->assertEquals([
            'name' => 'Marketing',
            'color' => '#ff0000',
        ], $array);
    }

    public function test_to_array_excludes_null_values(): void
    {
        $tagData = new TagData(
            name: 'Marketing'
        );

        $array = $tagData->toArray();

        $this->assertEquals(['name' => 'Marketing'], $array);
        $this->assertArrayNotHasKey('color', $array);
    }
}
