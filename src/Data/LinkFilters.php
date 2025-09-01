<?php

namespace KemokRepos\Larabrandly\Data;

class LinkFilters
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?string $orderBy = null,
        public readonly ?string $orderDir = null,
        public readonly ?string $domain = null,
        public readonly ?bool $favourite = null,
        public readonly ?array $tags = null,
        public readonly ?string $slashtag = null,
        public readonly ?string $title = null,
        public readonly ?\DateTimeImmutable $createdBefore = null,
        public readonly ?\DateTimeImmutable $createdAfter = null,
        public readonly ?\DateTimeImmutable $modifiedBefore = null,
        public readonly ?\DateTimeImmutable $modifiedAfter = null,
    ) {
    }

    public function toArray(): array
    {
        $filters = [];

        if ($this->limit !== null) {
            $filters['limit'] = $this->limit;
        }

        if ($this->offset !== null) {
            $filters['offset'] = $this->offset;
        }

        if ($this->orderBy !== null) {
            $filters['orderBy'] = $this->orderBy;
        }

        if ($this->orderDir !== null) {
            $filters['orderDir'] = $this->orderDir;
        }

        if ($this->domain !== null) {
            $filters['domain'] = $this->domain;
        }

        if ($this->favourite !== null) {
            $filters['favourite'] = $this->favourite;
        }

        if ($this->tags !== null) {
            $filters['tags'] = implode(',', $this->tags);
        }

        if ($this->slashtag !== null) {
            $filters['slashtag'] = $this->slashtag;
        }

        if ($this->title !== null) {
            $filters['title'] = $this->title;
        }

        if ($this->createdBefore !== null) {
            $filters['createdBefore'] = $this->createdBefore->format('Y-m-d\TH:i:s\Z');
        }

        if ($this->createdAfter !== null) {
            $filters['createdAfter'] = $this->createdAfter->format('Y-m-d\TH:i:s\Z');
        }

        if ($this->modifiedBefore !== null) {
            $filters['modifiedBefore'] = $this->modifiedBefore->format('Y-m-d\TH:i:s\Z');
        }

        if ($this->modifiedAfter !== null) {
            $filters['modifiedAfter'] = $this->modifiedAfter->format('Y-m-d\TH:i:s\Z');
        }

        return $filters;
    }
}
