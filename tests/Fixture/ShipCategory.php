<?php

namespace MichaelKaefer\Table\Tests\Fixture;

use MichaelKaefer\Table\Attribute\AsTableType;

#[AsTableType]
final readonly class ShipCategory
{
    public function __construct(
        public string $name,
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
