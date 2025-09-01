<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Services;

use KemokRepos\Larabrandly\Data\CreateLinkData;
use KemokRepos\Larabrandly\Data\LinkData;
use KemokRepos\Larabrandly\Data\UpdateLinkData;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;
use KemokRepos\Larabrandly\Services\RebrandlyService;
use PHPUnit\Framework\TestCase;

class RebrandlyServiceTest extends TestCase
{
    private RebrandlyClient $client;
    private RebrandlyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(RebrandlyClient::class);
        $this->service = new RebrandlyService($this->client);
    }

    public function test_create_link_success(): void
    {
        $createData = new CreateLinkData(
            destination: 'https://example.com',
            title: 'Test Link'
        );

        $responseData = [
            'id' => 'abc123',
            'title' => 'Test Link',
            'destination' => 'https://example.com',
            'shortUrl' => 'https://rebrand.ly/test',
        ];

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with('links', $createData->toArray())
            ->willReturn($responseData);

        $result = $this->service->createLink($createData);

        $this->assertInstanceOf(LinkData::class, $result);
        $this->assertEquals('abc123', $result->id);
        $this->assertEquals('Test Link', $result->title);
        $this->assertEquals('https://example.com', $result->destination);
    }

    public function test_create_link_throws_exception_when_no_id_in_response(): void
    {
        $createData = new CreateLinkData(destination: 'https://example.com');

        $this->client
            ->expects($this->once())
            ->method('post')
            ->willReturn([]);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Link creation response missing ID');

        $this->service->createLink($createData);
    }

    public function test_get_link_success(): void
    {
        $linkId = 'abc123';
        $responseData = [
            'id' => 'abc123',
            'title' => 'Test Link',
            'destination' => 'https://example.com',
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with("links/{$linkId}")
            ->willReturn($responseData);

        $result = $this->service->getLink($linkId);

        $this->assertInstanceOf(LinkData::class, $result);
        $this->assertEquals('abc123', $result->id);
    }

    public function test_get_link_throws_not_found_exception(): void
    {
        $linkId = 'nonexistent';

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException(RebrandlyException::apiError('Link not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Link with ID 'nonexistent' not found");

        $this->service->getLink($linkId);
    }

    public function test_update_link_success(): void
    {
        $linkId = 'abc123';
        $updateData = new UpdateLinkData(title: 'Updated Title');

        $responseData = [
            'id' => 'abc123',
            'title' => 'Updated Title',
            'destination' => 'https://example.com',
        ];

        $this->client
            ->expects($this->once())
            ->method('put')
            ->with("links/{$linkId}", $updateData->toArray())
            ->willReturn($responseData);

        $result = $this->service->updateLink($linkId, $updateData);

        $this->assertInstanceOf(LinkData::class, $result);
        $this->assertEquals('abc123', $result->id);
        $this->assertEquals('Updated Title', $result->title);
    }

    public function test_update_link_throws_not_found_exception(): void
    {
        $linkId = 'nonexistent';
        $updateData = new UpdateLinkData(title: 'Updated Title');

        $this->client
            ->expects($this->once())
            ->method('put')
            ->willThrowException(RebrandlyException::apiError('Link not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Link with ID 'nonexistent' not found");

        $this->service->updateLink($linkId, $updateData);
    }

    public function test_delete_link_success(): void
    {
        $linkId = 'abc123';

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->with("links/{$linkId}")
            ->willReturn([]);

        $result = $this->service->deleteLink($linkId);

        $this->assertTrue($result);
    }

    public function test_delete_link_throws_not_found_exception(): void
    {
        $linkId = 'nonexistent';

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(RebrandlyException::apiError('Link not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Link with ID 'nonexistent' not found");

        $this->service->deleteLink($linkId);
    }

    public function test_list_links_success(): void
    {
        $responseData = [
            [
                'id' => 'abc123',
                'title' => 'Link 1',
                'destination' => 'https://example.com',
            ],
            [
                'id' => 'def456',
                'title' => 'Link 2',
                'destination' => 'https://example2.com',
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('links', [])
            ->willReturn($responseData);

        $result = $this->service->listLinks();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(LinkData::class, $result[0]);
        $this->assertInstanceOf(LinkData::class, $result[1]);
        $this->assertEquals('abc123', $result[0]->id);
        $this->assertEquals('def456', $result[1]->id);
    }

    public function test_list_links_with_filters(): void
    {
        $filters = ['limit' => 10, 'orderBy' => 'createdAt'];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('links', $filters)
            ->willReturn([]);

        $result = $this->service->listLinks($filters);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}