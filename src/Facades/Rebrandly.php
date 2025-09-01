<?php

namespace KemokRepos\Larabrandly\Facades;

use Illuminate\Support\Facades\Facade;
use KemokRepos\Larabrandly\Data\AccountData;
use KemokRepos\Larabrandly\Data\CreateLinkData;
use KemokRepos\Larabrandly\Data\LinkData;
use KemokRepos\Larabrandly\Data\LinkFilters;
use KemokRepos\Larabrandly\Data\UpdateLinkData;

/**
 * @method static AccountData getAccount()
 * @method static LinkData createLink(CreateLinkData $data)
 * @method static LinkData getLink(string $linkId)
 * @method static LinkData updateLink(string $linkId, UpdateLinkData $data)
 * @method static bool deleteLink(string $linkId)
 * @method static LinkData[] listLinks(array|LinkFilters $filters = [])
 * @method static bool attachTagToLink(string $linkId, string $tagId)
 * @method static bool detachTagFromLink(string $linkId, string $tagId)
 * @method static array getLinkTags(string $linkId)
 * @method static LinkData[] getTagLinks(string $tagId, array $filters = [])
 *
 * @see \KemokRepos\Larabrandly\Services\RebrandlyService
 */
class Rebrandly extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'rebrandly';
    }
}
