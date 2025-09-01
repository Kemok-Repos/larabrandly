<?php

namespace KemokRepos\Larabrandly\Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use KemokRepos\Larabrandly\Data\CreateTagData;
use KemokRepos\Larabrandly\Data\TagData;
use KemokRepos\Larabrandly\Data\UpdateTagData;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;
use KemokRepos\Larabrandly\Services\TagService;
use PHPUnit\Framework\TestCase;

class TagIntegrationTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = require __DIR__ . '/../fixtures/api_responses.php';
    }

    public function test_complete_tag_lifecycle(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($this->fixtures['create_tag_success']) ?: '{}'),
            new Response(200, [], json_encode($this->fixtures['get_tag_success']) ?: '{}'),
            new Response(200, [], json_encode($this->fixtures['update_tag_success']) ?: '{}'),
            new Response(204, [], ''),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $createData = new CreateTagData(
            name: 'Marketing',
            color: '#ff6b35'
        );

        $createdTag = $service->createTag($createData);

        $this->assertInstanceOf(TagData::class, $createdTag);
        $this->assertEquals('tag123def', $createdTag->id);
        $this->assertEquals('Marketing', $createdTag->name);
        $this->assertEquals('#ff6b35', $createdTag->color);
        $this->assertEquals(0, $createdTag->linksCount);

        $this->assertNotNull($createdTag->id);
        $fetchedTag = $service->getTag($createdTag->id);

        $this->assertEquals('Marketing', $fetchedTag->name);
        $this->assertEquals(25, $fetchedTag->linksCount);

        $updateData = new UpdateTagData(
            name: 'Updated Marketing',
            color: '#00ff00'
        );

        $updatedTag = $service->updateTag($createdTag->id, $updateData);

        $this->assertEquals('Updated Marketing', $updatedTag->name);
        $this->assertEquals('#00ff00', $updatedTag->color);
        $this->assertEquals(30, $updatedTag->linksCount);

        $deleted = $service->deleteTag($createdTag->id);

        $this->assertTrue($deleted);
    }

    public function test_create_tag_with_minimal_data(): void
    {
        $responseData = $this->fixtures['create_tag_success'];
        unset($responseData['color']);

        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($responseData) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $createData = new CreateTagData(name: 'Simple Tag');

        $result = $service->createTag($createData);

        $this->assertInstanceOf(TagData::class, $result);
        $this->assertEquals('Marketing', $result->name);
        $this->assertNull($result->color);
    }

    public function test_list_tags_integration(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($this->fixtures['list_tags_success']) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $tags = $service->listTags(['limit' => 10]);

        $this->assertIsArray($tags);
        $this->assertCount(3, $tags);
        $this->assertInstanceOf(TagData::class, $tags[0]);
        $this->assertInstanceOf(TagData::class, $tags[1]);
        $this->assertInstanceOf(TagData::class, $tags[2]);
        
        $this->assertEquals('Marketing', $tags[0]->name);
        $this->assertEquals('Campaign', $tags[1]->name);
        $this->assertEquals('Social Media', $tags[2]->name);
        $this->assertEquals(25, $tags[0]->linksCount);
        $this->assertEquals(15, $tags[1]->linksCount);
        $this->assertEquals(8, $tags[2]->linksCount);
    }

    public function test_tag_link_attachment_integration(): void
    {
        $mockHandler = new MockHandler([
            new Response(204, [], ''),
            new Response(200, [], json_encode($this->fixtures['link_tags_success']) ?: '{}'),
            new Response(204, [], ''),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $linkId = 'link123abc';
        $tagId = 'tag123def';

        $attached = $service->attachTagToLink($linkId, $tagId);
        $this->assertTrue($attached);

        $linkTags = $service->getLinkTags($linkId);
        $this->assertIsArray($linkTags);
        $this->assertCount(2, $linkTags);
        $this->assertInstanceOf(TagData::class, $linkTags[0]);
        $this->assertEquals('Marketing', $linkTags[0]->name);
        $this->assertEquals('Campaign', $linkTags[1]->name);

        $detached = $service->detachTagFromLink($linkId, $tagId);
        $this->assertTrue($detached);
    }

    public function test_handles_tag_not_found_errors(): void
    {
        $mockHandler = new MockHandler([
            new Response(404, [], json_encode($this->fixtures['error_not_found']) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Tag with ID 'nonexistent' not found");

        $service->getTag('nonexistent');
    }

    public function test_update_single_tag_field(): void
    {
        $responseData = $this->fixtures['update_tag_success'];
        $responseData['name'] = 'Only Name Updated';
        unset($responseData['color']);

        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($responseData) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $updateData = new UpdateTagData(name: 'Only Name Updated');

        $result = $service->updateTag('tag123def', $updateData);

        $this->assertEquals('Only Name Updated', $result->name);
        $this->assertEquals(30, $result->linksCount);
    }

    public function test_get_tag_links_integration(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($this->fixtures['tag_links_success']) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $tagId = 'tag123def';
        $links = $service->getTagLinks($tagId);

        $this->assertIsArray($links);
        $this->assertCount(3, $links);
        
        // Verificar que son objetos LinkData
        $this->assertInstanceOf(\KemokRepos\Larabrandly\Data\LinkData::class, $links[0]);
        $this->assertInstanceOf(\KemokRepos\Larabrandly\Data\LinkData::class, $links[1]);
        $this->assertInstanceOf(\KemokRepos\Larabrandly\Data\LinkData::class, $links[2]);
        
        // Verificar contenido de los links
        $this->assertEquals('Marketing Campaign Link', $links[0]->title);
        $this->assertEquals('Product Launch', $links[1]->title);
        $this->assertEquals('Newsletter Signup', $links[2]->title);
        
        $this->assertEquals(150, $links[0]->clicks);
        $this->assertEquals(89, $links[1]->clicks);
        $this->assertEquals(64, $links[2]->clicks);
        
        $this->assertTrue($links[0]->favourite);
        $this->assertFalse($links[1]->favourite);
        $this->assertTrue($links[2]->favourite);
        
        // Verificar URLs
        $this->assertEquals('https://rebrand.ly/marketing-camp', $links[0]->shortUrl);
        $this->assertEquals('https://rebrand.ly/product-launch', $links[1]->shortUrl);
        $this->assertEquals('https://rebrand.ly/newsletter', $links[2]->shortUrl);
    }

    public function test_get_tag_links_with_filters(): void
    {
        $filteredData = array_slice($this->fixtures['tag_links_success'], 0, 2);
        
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($filteredData) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $filters = [
            'limit' => 2,
            'orderBy' => 'clicks',
            'orderDir' => 'desc',
        ];

        $links = $service->getTagLinks('tag123def', $filters);

        $this->assertIsArray($links);
        $this->assertCount(2, $links);
        $this->assertEquals('Marketing Campaign Link', $links[0]->title);
        $this->assertEquals('Product Launch', $links[1]->title);
    }

    public function test_get_tag_links_handles_not_found(): void
    {
        $mockHandler = new MockHandler([
            new Response(404, [], json_encode($this->fixtures['error_not_found']) ?: '{}'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new TagService($client);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Tag with ID 'nonexistent-tag' not found");

        $service->getTagLinks('nonexistent-tag');
    }
}
