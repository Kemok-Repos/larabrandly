<?php

namespace KemokRepos\Larabrandly\Data;

class LinkData
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $slashtag = null,
        public readonly ?string $destination = null,
        public readonly ?string $domain = null,
        public readonly ?string $shortUrl = null,
        public readonly ?array $tags = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
        public readonly ?int $clicks = null,
        public readonly ?bool $favourite = null,
        public readonly ?string $description = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slashtag: $data['slashtag'] ?? null,
            destination: $data['destination'] ?? null,
            domain: $data['domain']['fullName'] ?? ($data['domain'] ?? null),
            shortUrl: $data['shortUrl'] ?? null,
            tags: $data['tags'] ?? null,
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable($data['createdAt']) : null,
            updatedAt: isset($data['updatedAt']) ? new \DateTimeImmutable($data['updatedAt']) : null,
            clicks: $data['clicks'] ?? null,
            favourite: $data['favourite'] ?? null,
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        if ($this->slashtag !== null) {
            $data['slashtag'] = $this->slashtag;
        }

        if ($this->destination !== null) {
            $data['destination'] = $this->destination;
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

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
