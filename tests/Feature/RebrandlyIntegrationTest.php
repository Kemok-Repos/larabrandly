<?php

namespace KemokRepos\Larabrandly\Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use KemokRepos\Larabrandly\Data\CreateLinkData;
use KemokRepos\Larabrandly\Data\LinkData;
use KemokRepos\Larabrandly\Data\UpdateLinkData;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;
use KemokRepos\Larabrandly\Services\RebrandlyService;
use PHPUnit\Framework\TestCase;

class RebrandlyIntegrationTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = require __DIR__ . '/../fixtures/api_responses.php';
    }

    public function test_complete_link_lifecycle(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($this->fixtures['create_link_success'])),
            new Response(200, [], json_encode($this->fixtures['get_link_success'])),
            new Response(200, [], json_encode($this->fixtures['update_link_success'])),
            new Response(204, [], ''),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        $createData = new CreateLinkData(
            destination: 'https://example.com',
            slashtag: 'test',
            title: 'Test Link',
            description: 'Test link description',
            tags: ['tag1', 'tag2']
        );

        $createdLink = $service->createLink($createData);

        $this->assertInstanceOf(LinkData::class, $createdLink);
        $this->assertEquals('abc123def456', $createdLink->id);
        $this->assertEquals('Test Link', $createdLink->title);
        $this->assertEquals('https://example.com', $createdLink->destination);
        $this->assertEquals('https://rebrand.ly/test', $createdLink->shortUrl);

        $fetchedLink = $service->getLink($createdLink->id);

        $this->assertEquals('Existing Link', $fetchedLink->title);
        $this->assertEquals(42, $fetchedLink->clicks);
        $this->assertTrue($fetchedLink->favourite);

        $updateData = new UpdateLinkData(
            title: 'Updated Link Title',
            description: 'Updated link description',
            tags: ['updated', 'link'],
            favourite: false
        );

        $updatedLink = $service->updateLink($createdLink->id, $updateData);

        $this->assertEquals('Updated Link Title', $updatedLink->title);
        $this->assertEquals('Updated link description', $updatedLink->description);
        $this->assertEquals(['updated', 'link'], $updatedLink->tags);
        $this->assertFalse($updatedLink->favourite);

        $deleted = $service->deleteLink($createdLink->id);

        $this->assertTrue($deleted);
    }

    public function test_create_link_with_minimal_data(): void
    {
        $responseData = $this->fixtures['create_link_success'];
        unset($responseData['title'], $responseData['tags'], $responseData['description']);

        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        $createData = new CreateLinkData(destination: 'https://example.com');

        $result = $service->createLink($createData);

        $this->assertInstanceOf(LinkData::class, $result);
        $this->assertEquals('https://example.com', $result->destination);
        $this->assertNull($result->title);
        $this->assertNull($result->tags);
        $this->assertNull($result->description);
    }

    public function test_list_links_integration(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($this->fixtures['list_links_success'])),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        $links = $service->listLinks(['limit' => 10]);

        $this->assertIsArray($links);
        $this->assertCount(2, $links);
        $this->assertInstanceOf(LinkData::class, $links[0]);
        $this->assertInstanceOf(LinkData::class, $links[1]);
        
        $this->assertEquals('First Link', $links[0]->title);
        $this->assertEquals('Second Link', $links[1]->title);
        $this->assertEquals(10, $links[0]->clicks);
        $this->assertEquals(5, $links[1]->clicks);
        $this->assertFalse($links[0]->favourite);
        $this->assertTrue($links[1]->favourite);
    }

    public function test_handles_api_errors_properly(): void
    {
        $mockHandler = new MockHandler([
            new Response(401, [], json_encode($this->fixtures['error_unauthorized'])),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Invalid API key provided');

        $service->listLinks();
    }

    public function test_handles_not_found_errors(): void
    {
        $mockHandler = new MockHandler([
            new Response(404, [], json_encode($this->fixtures['error_not_found'])),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Link with ID 'nonexistent' not found");

        $service->getLink('nonexistent');
    }

    public function test_handles_validation_errors(): void
    {
        $mockHandler = new MockHandler([
            new Response(422, [], json_encode($this->fixtures['error_validation'])),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        try {
            $createData = new CreateLinkData(destination: '');
            $service->createLink($createData);
            $this->fail('Should have thrown RebrandlyException');
        } catch (RebrandlyException $e) {
            $this->assertEquals('Validation failed', $e->getMessage());
            $this->assertEquals(422, $e->getCode());
            
            $context = $e->getContext();
            $this->assertArrayHasKey('errors', $context);
            $this->assertArrayHasKey('destination', $context['errors']);
            $this->assertArrayHasKey('slashtag', $context['errors']);
        }
    }

    public function test_update_single_field(): void
    {
        $responseData = $this->fixtures['update_link_success'];
        $responseData['title'] = 'Only Title Updated';

        $mockHandler = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new RebrandlyClient('test-api-key', $httpClient);
        $service = new RebrandlyService($client);

        $updateData = new UpdateLinkData(title: 'Only Title Updated');

        $result = $service->updateLink('abc123def456', $updateData);

        $this->assertEquals('Only Title Updated', $result->title);
        $this->assertEquals('https://existing.com', $result->destination);
    }
}