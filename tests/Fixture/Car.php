<?php

namespace MichaelKaefer\Table\Tests\Fixture;

use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;

#[AsTableType(['head' => false])]
final readonly class Car
{
    public function __construct(
        #[TableColumn]
        public string $name,
    ) {
    }
}
