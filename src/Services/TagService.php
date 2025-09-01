<?php

namespace KemokRepos\Larabrandly\Services;

use KemokRepos\Larabrandly\Data\CreateTagData;
use KemokRepos\Larabrandly\Data\TagData;
use KemokRepos\Larabrandly\Data\UpdateTagData;
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;
use KemokRepos\Larabrandly\Http\RebrandlyClient;

class TagService
{
    public function __construct(
        private RebrandlyClient $client
    ) {}

    public function createTag(CreateTagData $data): TagData
    {
        $response = $this->client->post('tags', $data->toArray());

        if (!isset($response['id'])) {
            throw RebrandlyException::invalidResponse('Tag creation response missing ID');
        }

        return TagData::fromArray($response);
    }

    public function getTag(string $tagId): TagData
    {
        try {
            $response = $this->client->get("tags/{$tagId}");

            return TagData::fromArray($response);
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::apiError("Tag with ID '{$tagId}' not found", 404);
            }

            throw $e;
        }
    }

    public function updateTag(string $tagId, UpdateTagData $data): TagData
    {
        try {
            $response = $this->client->put("tags/{$tagId}", $data->toArray());

            return TagData::fromArray($response);
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::apiError("Tag with ID '{$tagId}' not found", 404);
            }

            throw $e;
        }
    }

    public function deleteTag(string $tagId): bool
    {
        try {
            $this->client->delete("tags/{$tagId}");

            return true;
        } catch (RebrandlyException $e) {
            if (str_contains($e->getMessage(), 'not found') || $e->getCode() === 404) {
                throw RebrandlyException::apiError("Tag with ID '{$tagId}' not found", 404);
            }

            throw $e;
        }
    }

    public function listTags(array $filters = []): array
    {
        $response = $this->client->get('tags', $filters);

        return array_map(
            fn (array $tag) => TagData::fromArray($tag),
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

            return array_map(
                fn (array $tag) => TagData::fromArray($tag),
                $response
            );
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
                fn (array $link) => \KemokRepos\Larabrandly\Data\LinkData::fromArray($link),
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