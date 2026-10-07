<?php

namespace MichaelKaefer\Table\Tests\Fixture\TableType;

use MichaelKaefer\Table\TableBuilder;
use MichaelKaefer\Table\TableTypeInterface;

class EmptyTableType implements TableTypeInterface
{
    public function buildTable(TableBuilder $builder): void
    {
    }
}
