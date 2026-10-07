<?php

namespace MichaelKaefer\Table\Tests\Fixture;

final readonly class PageCategory
{
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private iterable $pages = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPages(): iterable
    {
        return $this->pages;
    }
}
