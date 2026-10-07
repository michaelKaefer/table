<?php

namespace MichaelKaefer\Table\Tests\Fixture;

use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;
use MichaelKaefer\Table\ColumnType\CollectionType;

#[AsTableType(['head' => false])]
final readonly class Ship
{
    public function __construct(
        #[TableColumn(CollectionType::class)]
        public iterable $shipCategories,
    ) {
    }
}
