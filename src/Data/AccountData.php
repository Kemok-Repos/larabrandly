<?php

namespace KemokRepos\Larabrandly\Data;

class AccountData
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $username = null,
        public readonly ?string $email = null,
        public readonly ?string $fullName = null,
        public readonly ?string $avatarUrl = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?array $subscription = null,
        public readonly ?array $limits = null,
        public readonly ?array $usage = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            username: $data['username'] ?? null,
            email: $data['email'] ?? null,
            fullName: $data['fullName'] ?? null,
            avatarUrl: $data['avatarUrl'] ?? null,
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable($data['createdAt']) : null,
            subscription: $data['subscription'] ?? null,
            limits: $data['limits'] ?? null,
            usage: $data['usage'] ?? null,
        );
    }
}
