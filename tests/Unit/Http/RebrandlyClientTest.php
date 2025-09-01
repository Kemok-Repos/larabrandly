<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class RebrandlyClientTest extends TestCase
{
    private Client $httpClient;
    private RebrandlyClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(Client::class);
        $this->client = new RebrandlyClient('test-api-key', $this->httpClient);
    }

    public function test_get_request_success(): void
    {
        $responseData = ['id' => 'abc123', 'title' => 'Test'];
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn(json_encode($responseData));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'links/abc123', ['query' => []])
            ->willReturn($response);

        $result = $this->client->get('links/abc123');

        $this->assertEquals($responseData, $result);
    }

    public function test_post_request_success(): void
    {
        $requestData = ['destination' => 'https://example.com'];
        $responseData = ['id' => 'abc123', 'destination' => 'https://example.com'];

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn(json_encode($responseData));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'links', ['json' => $requestData])
            ->willReturn($response);

        $result = $this->client->post('links', $requestData);

        $this->assertEquals($responseData, $result);
    }

    public function test_put_request_success(): void
    {
        $requestData = ['title' => 'Updated Title'];
        $responseData = ['id' => 'abc123', 'title' => 'Updated Title'];

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn(json_encode($responseData));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('PUT', 'links/abc123', ['json' => $requestData])
            ->willReturn($response);

        $result = $this->client->put('links/abc123', $requestData);

        $this->assertEquals($responseData, $result);
    }

    public function test_delete_request_success(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('DELETE', 'links/abc123', [])
            ->willReturn($response);

        $result = $this->client->delete('links/abc123');

        $this->assertEquals([], $result);
    }

    public function test_handles_401_unauthorized_error(): void
    {
        $request = new Request('GET', 'links');
        $response = new Response(401, [], json_encode(['message' => 'Unauthorized']));

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new ClientException('Unauthorized', $request, $response));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Invalid API key provided');

        $this->client->get('links');
    }

    public function test_handles_404_not_found_error(): void
    {
        $request = new Request('GET', 'links/nonexistent');
        $response = new Response(404, [], json_encode(['message' => 'Not Found']));

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new ClientException('Not Found', $request, $response));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage("Link with ID '' not found");

        $this->client->get('links/nonexistent');
    }

    public function test_handles_generic_client_error(): void
    {
        $request = new Request('GET', 'links');
        $errorData = ['message' => 'Validation failed', 'errors' => ['field' => 'required']];
        $response = new Response(422, [], json_encode($errorData));

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new ClientException('Validation failed', $request, $response));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Validation failed');

        $exception = null;
        try {
            $this->client->get('links');
        } catch (RebrandlyException $e) {
            $exception = $e;
            throw $e;
        } finally {
            if ($exception) {
                $this->assertEquals($errorData, $exception->getContext());
                $this->assertEquals(422, $exception->getCode());
            }
        }
    }

    public function test_handles_network_error(): void
    {
        $request = new Request('GET', 'links');

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new RequestException('Connection timeout', $request));

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Network error: Connection timeout');

        $this->client->get('links');
    }

    public function test_handles_invalid_json_response(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('invalid json');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $this->expectException(RebrandlyException::class);
        $this->expectExceptionMessage('Invalid JSON response');

        $this->client->get('links');
    }

    public function test_handles_empty_response(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $result = $this->client->get('links');

        $this->assertEquals([], $result);
    }
}