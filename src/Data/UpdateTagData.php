<?php

namespace KemokRepos\Larabrandly\Data;

class UpdateTagData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $color = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->color !== null) {
            $data['color'] = $this->color;
        }

        return $data;
    }
}
