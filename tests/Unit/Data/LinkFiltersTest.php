<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Data;

use KemokRepos\Larabrandly\Data\LinkFilters;
use PHPUnit\Framework\TestCase;

class LinkFiltersTest extends TestCase
{
    public function test_can_create_with_no_filters(): void
    {
        $filters = new LinkFilters();

        $this->assertNull($filters->limit);
        $this->assertNull($filters->offset);
        $this->assertNull($filters->orderBy);
        $this->assertNull($filters->orderDir);
        $this->assertNull($filters->domain);
        $this->assertNull($filters->favourite);
        $this->assertNull($filters->tags);
        $this->assertNull($filters->slashtag);
        $this->assertNull($filters->title);
    }

    public function test_can_create_with_all_filters(): void
    {
        $createdAfter = new \DateTimeImmutable('2023-01-01T00:00:00Z');
        $createdBefore = new \DateTimeImmutable('2023-12-31T23:59:59Z');
        
        $filters = new LinkFilters(
            limit: 10,
            offset: 20,
            orderBy: 'createdAt',
            orderDir: 'desc',
            domain: 'rebrand.ly',
            favourite: true,
            tags: ['marketing', 'campaign'],
            slashtag: 'test',
            title: 'Test Link',
            createdAfter: $createdAfter,
            createdBefore: $createdBefore
        );

        $this->assertEquals(10, $filters->limit);
        $this->assertEquals(20, $filters->offset);
        $this->assertEquals('createdAt', $filters->orderBy);
        $this->assertEquals('desc', $filters->orderDir);
        $this->assertEquals('rebrand.ly', $filters->domain);
        $this->assertTrue($filters->favourite);
        $this->assertEquals(['marketing', 'campaign'], $filters->tags);
        $this->assertEquals('test', $filters->slashtag);
        $this->assertEquals('Test Link', $filters->title);
        $this->assertEquals($createdAfter, $filters->createdAfter);
        $this->assertEquals($createdBefore, $filters->createdBefore);
    }

    public function test_to_array_returns_empty_when_no_filters_set(): void
    {
        $filters = new LinkFilters();

        $array = $filters->toArray();

        $this->assertEmpty($array);
    }

    public function test_to_array_includes_only_non_null_values(): void
    {
        $filters = new LinkFilters(
            limit: 10,
            favourite: true,
            tags: ['marketing', 'campaign']
        );

        $array = $filters->toArray();

        $expected = [
            'limit' => 10,
            'favourite' => true,
            'tags' => 'marketing,campaign',
        ];

        $this->assertEquals($expected, $array);
        $this->assertArrayNotHasKey('offset', $array);
        $this->assertArrayNotHasKey('orderBy', $array);
        $this->assertArrayNotHasKey('domain', $array);
    }

    public function test_to_array_handles_date_formatting(): void
    {
        $createdAfter = new \DateTimeImmutable('2023-01-01T12:30:45Z');
        $modifiedBefore = new \DateTimeImmutable('2023-12-31T23:59:59Z');

        $filters = new LinkFilters(
            createdAfter: $createdAfter,
            modifiedBefore: $modifiedBefore
        );

        $array = $filters->toArray();

        $this->assertEquals('2023-01-01T12:30:45Z', $array['createdAfter']);
        $this->assertEquals('2023-12-31T23:59:59Z', $array['modifiedBefore']);
    }

    public function test_to_array_includes_false_boolean_values(): void
    {
        $filters = new LinkFilters(favourite: false);

        $array = $filters->toArray();

        $this->assertArrayHasKey('favourite', $array);
        $this->assertFalse($array['favourite']);
    }

    public function test_to_array_handles_tags_as_string(): void
    {
        $filters = new LinkFilters(tags: ['tag1', 'tag2', 'tag3']);

        $array = $filters->toArray();

        $this->assertEquals('tag1,tag2,tag3', $array['tags']);
    }
}