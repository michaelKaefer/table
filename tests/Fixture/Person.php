<?php

namespace MichaelKaefer\Table\Tests\Fixture;

class Person
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?PersonCountry $country = null,
        public ?bool $active = null,
        public ?\DateTimeInterface $createdAt = null,
    ) {
    }
}
