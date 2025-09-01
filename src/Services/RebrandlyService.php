<?php

namespace KemokRepos\Larabrandly\Services;

use KemokRepos\Larabrandly\Data\AccountData;
use KemokRepos\Larabrandly\Data\CreateLinkData;
use KemokRepos\Larabrandly\Data\LinkData;
use KemokRepos\Larabrandly\Data\LinkFilters;
use KemokRepos\Larabrandly\Data\UpdateLinkData;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;

class RebrandlyService
{
    public function __construct(
        private RebrandlyClient $client
    ) {
    }

    public function getAccount(): AccountData
    {
        $response = $this->client->get('account');

        return AccountData::fromArray($response);
    }

    public function createLink(CreateLinkData $data): LinkData
    {
        $response = $this->client->post('links', $data->toArray());

        if (!isset($response['id'])) {
            throw RebrandlyException::invalidResponse('Link creation response missing ID');
        }

        return LinkData::fromArray($response);
    }

    public function getLink(string $linkId): LinkData
    {
        try {
            $response = $this->client->get("links/{$linkId}");

            return LinkData::fromArray($response);
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::linkNotFound($linkId);
            }

            throw $e;
        }
    }

    public function updateLink(string $linkId, UpdateLinkData $data): LinkData
    {
        try {
            $response = $this->client->put("links/{$linkId}", $data->toArray());

            return LinkData::fromArray($response);
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::linkNotFound($linkId);
            }

            throw $e;
        }
    }

    public function deleteLink(string $linkId): bool
    {
        try {
            $this->client->delete("links/{$linkId}");

            return true;
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::linkNotFound($linkId);
            }

            throw $e;
        }
    }

    public function listLinks(array|LinkFilters $filters = []): array
    {
        if ($filters instanceof LinkFilters) {
            $filters = $filters->toArray();
        }

        $response = $this->client->get('links', $filters);

        return array_map(
            fn (array $link) => LinkData::fromArray($link),
            $response
        );
    }

    public function attachTagToLink(string $linkId, string $tagId): bool
    {
        try {
            $this->client->post("links/{$linkId}/tags/{$tagId}");

            return true;
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::apiError("Link or Tag not found", 404);
            }

            throw $e;
        }
    }

    public function detachTagFromLink(string $linkId, string $tagId): bool
    {
        try {
            $this->client->delete("links/{$linkId}/tags/{$tagId}");

            return true;
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::apiError("Link or Tag not found", 404);
            }

            throw $e;
        }
    }

    public function getLinkTags(string $linkId): array
    {
        try {
            $response = $this->client->get("links/{$linkId}/tags");

            return $response;
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::linkNotFound($linkId);
            }

            throw $e;
        }
    }

    public function getTagLinks(string $tagId, array $filters = []): array
    {
        try {
            $response = $this->client->get("tags/{$tagId}/links", $filters);

            return array_map(
                fn (array $link) => LinkData::fromArray($link),
                $response
            );
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::apiError("Tag with ID '{$tagId}' not found", 404);
            }

            throw $e;
        }
    }
}
