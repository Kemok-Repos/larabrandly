<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Data;

use KemokRepos\Larabrandly\Data\CreateLinkData;
use PHPUnit\Framework\TestCase;

class CreateLinkDataTest extends TestCase
{
    public function test_can_create_with_required_fields_only(): void
    {
        $data = new CreateLinkData(
            destination: 'https://example.com'
        );

        $this->assertEquals('https://example.com', $data->destination);
        $this->assertNull($data->slashtag);
        $this->assertNull($data->title);
        $this->assertNull($data->description);
        $this->assertNull($data->domain);
        $this->assertNull($data->tags);
        $this->assertNull($data->favourite);
    }

    public function test_can_create_with_all_fields(): void
    {
        $data = new CreateLinkData(
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

    public function test_to_array_includes_destination(): void
    {
        $data = new CreateLinkData(
            destination: 'https://example.com'
        );

        $array = $data->toArray();

        $this->assertArrayHasKey('destination', $array);
        $this->assertEquals('https://example.com', $array['destination']);
    }

    public function test_to_array_includes_only_non_null_values(): void
    {
        $data = new CreateLinkData(
            destination: 'https://example.com',
            title: 'Test Link',
            tags: ['tag1', 'tag2']
        );

        $array = $data->toArray();

        $expected = [
            'destination' => 'https://example.com',
            'title' => 'Test Link',
            'tags' => ['tag1', 'tag2'],
        ];

        $this->assertEquals($expected, $array);
        $this->assertArrayNotHasKey('slashtag', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('domain', $array);
        $this->assertArrayNotHasKey('favourite', $array);
    }

    public function test_to_array_includes_false_boolean_values(): void
    {
        $data = new CreateLinkData(
            destination: 'https://example.com',
            favourite: false
        );

        $array = $data->toArray();

        $this->assertArrayHasKey('favourite', $array);
        $this->assertFalse($array['favourite']);
    }
}
