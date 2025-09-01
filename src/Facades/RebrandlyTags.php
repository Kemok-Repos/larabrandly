<?php

namespace KemokRepos\Larabrandly\Facades;

use Illuminate\Support\Facades\Facade;
use KemokRepos\Larabrandly\Data\CreateTagData;
use KemokRepos\Larabrandly\Data\TagData;
use KemokRepos\Larabrandly\Data\UpdateTagData;

/**
 * @method static TagData createTag(CreateTagData $data)
 * @method static TagData getTag(string $tagId)
 * @method static TagData updateTag(string $tagId, UpdateTagData $data)
 * @method static bool deleteTag(string $tagId)
 * @method static TagData[] listTags(array $filters = [])
 * @method static bool attachTagToLink(string $linkId, string $tagId)
 * @method static bool detachTagFromLink(string $linkId, string $tagId)
 * @method static TagData[] getLinkTags(string $linkId)
 * @method static \KemokRepos\Larabrandly\Data\LinkData[] getTagLinks(string $tagId, array $filters = [])
 *
 * @see \KemokRepos\Larabrandly\Services\TagService
 */
class RebrandlyTags extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'rebrandly-tags';
    }
}