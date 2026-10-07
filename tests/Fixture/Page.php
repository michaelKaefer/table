<?php

namespace MichaelKaefer\Table\Tests\Fixture;

use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;
use MichaelKaefer\Table\ColumnType\CollectionType;

#[AsTableType]
final readonly class Page implements LinkableInterface
{
    public function __construct(
        #[TableColumn]
        private int $id,
        #[TableColumn]
        private string $name,
        #[TableColumn(CollectionType::class)]
        private iterable $pageCategories = [],
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPageCategories(): iterable
    {
        return $this->pageCategories;
    }
}
