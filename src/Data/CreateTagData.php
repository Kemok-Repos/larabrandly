<?php

namespace KemokRepos\Larabrandly\Data;

class CreateTagData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $color = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
        ];

        if ($this->color !== null) {
            $data['color'] = $this->color;
        }

        return $data;
    }
}
