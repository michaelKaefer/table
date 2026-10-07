<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class RoomDesk
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public array $screens = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
