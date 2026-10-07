<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class RoomDeskScreen implements LinkableInterface
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public array $brands = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
