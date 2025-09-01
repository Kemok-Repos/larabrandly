<?php

namespace KemokRepos\Larabrandly\Data;

class UpdateLinkData
{
    public function __construct(
        public readonly ?string $destination = null,
        public readonly ?string $slashtag = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $domain = null,
        public readonly ?array $tags = null,
        public readonly ?bool $favourite = null,
    ) {}

    public function toArray(): array
    {
        $data = [];

        if ($this->destination !== null) {
            $data['destination'] = $this->destination;
        }

        if ($this->slashtag !== null) {
            $data['slashtag'] = $this->slashtag;
        }

        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->domain !== null) {
            $data['domain'] = $this->domain;
        }

        if ($this->tags !== null) {
            $data['tags'] = $this->tags;
        }

        if ($this->favourite !== null) {
            $data['favourite'] = $this->favourite;
        }

        return $data;
    }
}