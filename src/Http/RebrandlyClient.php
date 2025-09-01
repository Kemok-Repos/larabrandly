<?php

namespace KemokRepos\Larabrandly\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use Psr\Http\Message\ResponseInterface;

class RebrandlyClient
{
    private const BASE_URL = 'https://api.rebrandly.com/v1/';

    private Client $httpClient;
    private string $apiKey;

    public function __construct(string $apiKey, ?Client $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 30,
            'headers' => [
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, [
            'query' => $query,
        ]);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, [
            'json' => $data,
        ]);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, [
            'json' => $data,
        ]);
    }

    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    private function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, $options);

            return $this->parseResponse($response);
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (RequestException $e) {
            throw RebrandlyException::networkError($e->getMessage());
        } catch (GuzzleException $e) {
            throw RebrandlyException::networkError($e->getMessage());
        }
    }

    private function parseResponse(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();

        if (empty($content)) {
            return [];
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw RebrandlyException::invalidResponse('Invalid JSON response');
        }

        return $data;
    }

    /**
     * @return never
     */
    private function handleClientException(ClientException $e): void
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $content = $response->getBody()->getContents();

        $errorData = json_decode($content, true) ?? [];

        match ($statusCode) {
            401 => throw RebrandlyException::invalidApiKey(),
            404 => throw RebrandlyException::linkNotFound(''),
            default => throw RebrandlyException::apiError(
                $errorData['message'] ?? 'API Error',
                $statusCode,
                $errorData
            ),
        };
    }
}
