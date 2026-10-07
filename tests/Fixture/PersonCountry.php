<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class PersonCountry
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?PersonCountryCategory $category = null,
        public array $tags = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
