<?php

namespace MichaelKaefer\Table\Tests\Fixture;

use MichaelKaefer\Table\Attribute\AsTableType;

#[AsTableType(['head' => false])]
final readonly class Book implements LinkableInterface
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
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
