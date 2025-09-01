<?php

namespace KemokRepos\Larabrandly\Tests\Unit\Data;

use KemokRepos\Larabrandly\Data\AccountData;
use PHPUnit\Framework\TestCase;

class AccountDataTest extends TestCase
{
    public function test_can_create_from_array_with_all_fields(): void
    {
        $data = [
            'id' => 'user123',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'fullName' => 'Test User',
            'avatarUrl' => 'https://example.com/avatar.jpg',
            'createdAt' => '2023-01-01T12:00:00Z',
            'subscription' => [
                'plan' => 'free',
                'status' => 'active',
            ],
            'limits' => [
                'links' => 1000,
                'clicks' => 10000,
            ],
            'usage' => [
                'links' => 50,
                'clicks' => 500,
            ],
        ];

        $accountData = AccountData::fromArray($data);

        $this->assertEquals('user123', $accountData->id);
        $this->assertEquals('testuser', $accountData->username);
        $this->assertEquals('test@example.com', $accountData->email);
        $this->assertEquals('Test User', $accountData->fullName);
        $this->assertEquals('https://example.com/avatar.jpg', $accountData->avatarUrl);
        $this->assertInstanceOf(\DateTimeImmutable::class, $accountData->createdAt);
        $this->assertEquals('2023-01-01T12:00:00+00:00', $accountData->createdAt->format('c'));
        $this->assertEquals(['plan' => 'free', 'status' => 'active'], $accountData->subscription);
        $this->assertEquals(['links' => 1000, 'clicks' => 10000], $accountData->limits);
        $this->assertEquals(['links' => 50, 'clicks' => 500], $accountData->usage);
    }

    public function test_can_create_from_array_with_minimal_fields(): void
    {
        $data = [
            'id' => 'user123',
            'username' => 'testuser',
        ];

        $accountData = AccountData::fromArray($data);

        $this->assertEquals('user123', $accountData->id);
        $this->assertEquals('testuser', $accountData->username);
        $this->assertNull($accountData->email);
        $this->assertNull($accountData->fullName);
        $this->assertNull($accountData->avatarUrl);
        $this->assertNull($accountData->createdAt);
        $this->assertNull($accountData->subscription);
        $this->assertNull($accountData->limits);
        $this->assertNull($accountData->usage);
    }
}
