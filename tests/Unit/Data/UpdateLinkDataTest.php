<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Data;

use KemokRepos\Larabrandly\Data\UpdateLinkData;
use PHPUnit\Framework\TestCase;

class UpdateLinkDataTest extends TestCase
{
    public function test_can_create_with_no_fields(): void
    {
        $data = new UpdateLinkData();

        $this->assertNull($data->destination);
        $this->assertNull($data->slashtag);
        $this->assertNull($data->title);
        $this->assertNull($data->description);
        $this->assertNull($data->domain);
        $this->assertNull($data->tags);
        $this->assertNull($data->favourite);
    }

    public function test_can_create_with_all_fields(): void
    {
        $data = new UpdateLinkData(
            destination: 'https://example.com',
            slashtag: 'test',
            title: 'Test Link',
            description: 'Test description',
            domain: 'rebrand.ly',
            tags: ['tag1', 'tag2'],
            favourite: true
        );

        $this->assertEquals('https://example.com', $data->destination);
        $this->assertEquals('test', $data->slashtag);
        $this->assertEquals('Test Link', $data->title);
        $this->assertEquals('Test description', $data->description);
        $this->assertEquals('rebrand.ly', $data->domain);
        $this->assertEquals(['tag1', 'tag2'], $data->tags);
        $this->assertTrue($data->favourite);
    }

    public function test_to_array_returns_empty_when_no_fields_set(): void
    {
        $data = new UpdateLinkData();

        $array = $data->toArray();

        $this->assertEmpty($array);
    }

    public function test_to_array_includes_only_non_null_values(): void
    {
        $data = new UpdateLinkData(
            destination: 'https://example.com',
            title: 'Test Link'
        );

        $array = $data->toArray();

        $expected = [
            'destination' => 'https://example.com',
            'title' => 'Test Link',
        ];

        $this->assertEquals($expected, $array);
        $this->assertArrayNotHasKey('slashtag', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('domain', $array);
        $this->assertArrayNotHasKey('tags', $array);
        $this->assertArrayNotHasKey('favourite', $array);
    }

    public function test_to_array_includes_false_boolean_values(): void
    {
        $data = new UpdateLinkData(
            favourite: false
        );

        $array = $data->toArray();

        $this->assertArrayHasKey('favourite', $array);
        $this->assertFalse($array['favourite']);
    }

    public function test_can_update_single_field(): void
    {
        $data = new UpdateLinkData(title: 'New Title');

        $array = $data->toArray();

        $this->assertEquals(['title' => 'New Title'], $array);
    }
}