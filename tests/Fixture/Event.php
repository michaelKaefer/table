<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class Event
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public array $registrations = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
