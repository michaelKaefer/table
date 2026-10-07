<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class Room
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public array $desks = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
