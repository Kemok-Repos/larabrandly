<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Data;

use KemokRepos\Larabrandly\Data\LinkData;
use PHPUnit\Framework\TestCase;

class LinkDataTest extends TestCase
{
    public function test_can_create_from_array_with_all_fields(): void
    {
        $data = [
            'id' => 'abc123',
            'title' => 'Test Link',
            'slashtag' => 'test',
            'destination' => 'https://example.com',
            'domain' => ['fullName' => 'rebrand.ly'],
            'shortUrl' => 'https://rebrand.ly/test',
            'tags' => ['tag1', 'tag2'],
            'createdAt' => '2023-01-01T12:00:00Z',
            'updatedAt' => '2023-01-02T12:00:00Z',
            'clicks' => 42,
            'favourite' => true,
            'description' => 'Test description',
        ];

        $linkData = LinkData::fromArray($data);

        $this->assertEquals('abc123', $linkData->id);
        $this->assertEquals('Test Link', $linkData->title);
        $this->assertEquals('test', $linkData->slashtag);
        $this->assertEquals('https://example.com', $linkData->destination);
        $this->assertEquals('rebrand.ly', $linkData->domain);
        $this->assertEquals('https://rebrand.ly/test', $linkData->shortUrl);
        $this->assertEquals(['tag1', 'tag2'], $linkData->tags);
        $this->assertInstanceOf(\DateTimeImmutable::class, $linkData->createdAt);
        $this->assertEquals('2023-01-01T12:00:00+00:00', $linkData->createdAt->format('c'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $linkData->updatedAt);
        $this->assertEquals('2023-01-02T12:00:00+00:00', $linkData->updatedAt->format('c'));
        $this->assertEquals(42, $linkData->clicks);
        $this->assertTrue($linkData->favourite);
        $this->assertEquals('Test description', $linkData->description);
    }

    public function test_can_create_from_array_with_minimal_fields(): void
    {
        $data = [
            'id' => 'abc123',
            'destination' => 'https://example.com',
        ];

        $linkData = LinkData::fromArray($data);

        $this->assertEquals('abc123', $linkData->id);
        $this->assertEquals('https://example.com', $linkData->destination);
        $this->assertNull($linkData->title);
        $this->assertNull($linkData->slashtag);
        $this->assertNull($linkData->domain);
        $this->assertNull($linkData->shortUrl);
        $this->assertNull($linkData->tags);
        $this->assertNull($linkData->createdAt);
        $this->assertNull($linkData->updatedAt);
        $this->assertNull($linkData->clicks);
        $this->assertNull($linkData->favourite);
        $this->assertNull($linkData->description);
    }

    public function test_can_handle_domain_as_string(): void
    {
        $data = [
            'id' => 'abc123',
            'domain' => 'rebrand.ly',
            'destination' => 'https://example.com',
        ];

        $linkData = LinkData::fromArray($data);

        $this->assertEquals('rebrand.ly', $linkData->domain);
    }

    public function test_to_array_includes_only_non_null_values(): void
    {
        $linkData = new LinkData(
            title: 'Test Link',
            destination: 'https://example.com',
            tags: ['tag1', 'tag2']
        );

        $array = $linkData->toArray();

        $this->assertEquals([
            'title' => 'Test Link',
            'destination' => 'https://example.com',
            'tags' => ['tag1', 'tag2'],
        ], $array);
    }

    public function test_to_array_excludes_null_values(): void
    {
        $linkData = new LinkData(
            title: 'Test Link',
            destination: 'https://example.com'
        );

        $array = $linkData->toArray();

        $this->assertArrayNotHasKey('slashtag', $array);
        $this->assertArrayNotHasKey('domain', $array);
        $this->assertArrayNotHasKey('tags', $array);
        $this->assertArrayNotHasKey('favourite', $array);
        $this->assertArrayNotHasKey('description', $array);
    }
}