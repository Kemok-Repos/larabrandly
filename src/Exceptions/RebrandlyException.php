<?php

namespace KemokRepos\Larabrandly\Exceptions;

use Exception;

class RebrandlyException extends Exception
{
    /** @var array<string, mixed> */
    protected array $context = [];

    public static function invalidApiKey(): self
    {
        return new self('Invalid API key provided');
    }

    public static function linkNotFound(string $linkId): self
    {
        return new self("Link with ID '{$linkId}' not found");
    }

    public static function apiError(string $message, int $code = 0, array $context = []): self
    {
        $exception = new self($message, $code);
        $exception->context = $context;

        return $exception;
    }

    public static function invalidResponse(string $message = 'Invalid API response'): self
    {
        return new self($message);
    }

    public static function networkError(string $message): self
    {
        return new self("Network error: {$message}");
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
