<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Services;

use KemokRepos\Larabrandly\Data\CreateTagData;
use KemokRepos\Larabrandly\Data\LinkData;
use KemokRepos\Larabrandly\Data\TagData;
use KemokRepos\Larabrandly\Data\UpdateTagData;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;
use KemokRepos\Larabrandly\Services\TagService;
use PHPUnit\Framework\TestCase;

class TagServiceTest extends TestCase
{
    private RebrandlyClient $client;
    private TagService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(RebrandlyClient::class);
        $this->service = new TagService($this->client);
    }

    public function test_create_tag_success(): void
    {
        $createData = new CreateTagData(
            name: 'Marketing',
            color: '#ff0000'
        );

        $responseData = [
            'id' => 'tag123',
            'name' => 'Marketing',
            'color' => '#ff0000',
            'createdAt' => '2023-01-01T12:00:00Z',
            'linksCount' => 0,
        ];

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with('tags', $createData->toArray())
            ->willReturn($responseData);

        $result = $this->service->createTag($createData);

        $this->assertInstanceOf(TagData::class, $result);
        $this->assertEquals('tag123', $result->id);
        $this->assertEquals('Marketing', $result->name);
        $this->assertEquals('#ff0000', $result->color);
    }

    public function test_create_tag_throws_exception_when_no_id_in_response(): void
    {
        $createData = new CreateTagData(name: 'Marketing');

        $this->client
            ->expects($this->once())
            ->method('post')
            ->willReturn([]);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Tag creation response missing ID');

        $this->service->createTag($createData);
    }

    public function test_get_tag_success(): void
    {
        $tagId = 'tag123';
        $responseData = [
            'id' => 'tag123',
            'name' => 'Marketing',
            'color' => '#ff0000',
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with("tags/{$tagId}")
            ->willReturn($responseData);

        $result = $this->service->getTag($tagId);

        $this->assertInstanceOf(TagData::class, $result);
        $this->assertEquals('tag123', $result->id);
    }

    public function test_get_tag_throws_not_found_exception(): void
    {
        $tagId = 'nonexistent';

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException(RebrandlyException::apiError('Tag not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Tag with ID 'nonexistent' not found");

        $this->service->getTag($tagId);
    }

    public function test_update_tag_success(): void
    {
        $tagId = 'tag123';
        $updateData = new UpdateTagData(name: 'Updated Marketing');

        $responseData = [
            'id' => 'tag123',
            'name' => 'Updated Marketing',
            'color' => '#ff0000',
        ];

        $this->client
            ->expects($this->once())
            ->method('put')
            ->with("tags/{$tagId}", $updateData->toArray())
            ->willReturn($responseData);

        $result = $this->service->updateTag($tagId, $updateData);

        $this->assertInstanceOf(TagData::class, $result);
        $this->assertEquals('tag123', $result->id);
        $this->assertEquals('Updated Marketing', $result->name);
    }

    public function test_update_tag_throws_not_found_exception(): void
    {
        $tagId = 'nonexistent';
        $updateData = new UpdateTagData(name: 'Updated Name');

        $this->client
            ->expects($this->once())
            ->method('put')
            ->willThrowException(RebrandlyException::apiError('Tag not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Tag with ID 'nonexistent' not found");

        $this->service->updateTag($tagId, $updateData);
    }

    public function test_delete_tag_success(): void
    {
        $tagId = 'tag123';

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->with("tags/{$tagId}")
            ->willReturn([]);

        $result = $this->service->deleteTag($tagId);

        $this->assertTrue($result);
    }

    public function test_delete_tag_throws_not_found_exception(): void
    {
        $tagId = 'nonexistent';

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(RebrandlyException::apiError('Tag not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Tag with ID 'nonexistent' not found");

        $this->service->deleteTag($tagId);
    }

    public function test_list_tags_success(): void
    {
        $responseData = [
            [
                'id' => 'tag123',
                'name' => 'Marketing',
                'color' => '#ff0000',
            ],
            [
                'id' => 'tag456',
                'name' => 'Campaign',
                'color' => '#00ff00',
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('tags', [])
            ->willReturn($responseData);

        $result = $this->service->listTags();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(TagData::class, $result[0]);
        $this->assertInstanceOf(TagData::class, $result[1]);
        $this->assertEquals('tag123', $result[0]->id);
        $this->assertEquals('tag456', $result[1]->id);
    }

    public function test_list_tags_with_filters(): void
    {
        $filters = ['limit' => 10];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('tags', $filters)
            ->willReturn([]);

        $result = $this->service->listTags($filters);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_attach_tag_to_link_success(): void
    {
        $linkId = 'link123';
        $tagId = 'tag123';

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with("links/{$linkId}/tags/{$tagId}")
            ->willReturn([]);

        $result = $this->service->attachTagToLink($linkId, $tagId);

        $this->assertTrue($result);
    }

    public function test_attach_tag_to_link_throws_not_found_exception(): void
    {
        $linkId = 'nonexistent';
        $tagId = 'tag456';

        $this->client
            ->expects($this->once())
            ->method('post')
            ->willThrowException(RebrandlyException::apiError('Link not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Link or Tag not found');

        $this->service->attachTagToLink($linkId, $tagId);
    }

    public function test_detach_tag_from_link_success(): void
    {
        $linkId = 'link123';
        $tagId = 'tag123';

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->with("links/{$linkId}/tags/{$tagId}")
            ->willReturn([]);

        $result = $this->service->detachTagFromLink($linkId, $tagId);

        $this->assertTrue($result);
    }

    public function test_detach_tag_from_link_throws_not_found_exception(): void
    {
        $linkId = 'nonexistent';
        $tagId = 'tag456';

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(RebrandlyException::apiError('Tag not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Link or Tag not found');

        $this->service->detachTagFromLink($linkId, $tagId);
    }

    public function test_get_link_tags_success(): void
    {
        $linkId = 'link123';
        $responseData = [
            [
                'id' => 'tag123',
                'name' => 'Marketing',
            ],
            [
                'id' => 'tag456',
                'name' => 'Campaign',
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with("links/{$linkId}/tags")
            ->willReturn($responseData);

        $result = $this->service->getLinkTags($linkId);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(TagData::class, $result[0]);
        $this->assertInstanceOf(TagData::class, $result[1]);
    }

    public function test_get_link_tags_throws_not_found_exception(): void
    {
        $linkId = 'nonexistent';

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException(RebrandlyException::apiError('Link not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Link with ID 'nonexistent' not found");

        $this->service->getLinkTags($linkId);
    }

    public function test_get_tag_links_success(): void
    {
        $tagId = 'tag123';
        $responseData = [
            [
                'id' => 'link123',
                'title' => 'First Link',
                'destination' => 'https://first.com',
                'shortUrl' => 'https://rebrand.ly/first',
            ],
            [
                'id' => 'link456',
                'title' => 'Second Link',
                'destination' => 'https://second.com',
                'shortUrl' => 'https://rebrand.ly/second',
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with("tags/{$tagId}/links", [])
            ->willReturn($responseData);

        $result = $this->service->getTagLinks($tagId);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\KemokRepos\Larabrandly\Data\LinkData::class, $result[0]);
        $this->assertInstanceOf(\KemokRepos\Larabrandly\Data\LinkData::class, $result[1]);
        $this->assertEquals('link123', $result[0]->id);
        $this->assertEquals('link456', $result[1]->id);
        $this->assertEquals('First Link', $result[0]->title);
        $this->assertEquals('Second Link', $result[1]->title);
    }

    public function test_get_tag_links_with_filters(): void
    {
        $tagId = 'tag123';
        $filters = ['limit' => 10, 'orderBy' => 'createdAt'];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with("tags/{$tagId}/links", $filters)
            ->willReturn([]);

        $result = $this->service->getTagLinks($tagId, $filters);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_tag_links_throws_not_found_exception(): void
    {
        $tagId = 'nonexistent';

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException(RebrandlyException::apiError('Tag not found', 404));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Tag with ID 'nonexistent' not found");

        $this->service->getTagLinks($tagId);
    }
}
