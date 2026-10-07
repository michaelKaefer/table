<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class EventRegistration
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
