<?php

namespace MichaelKaefer\Table\Tests\Fixture\TableType;

use MichaelKaefer\Table\AbstractTableType;
use MichaelKaefer\Table\TableBuilder;

class EmptyTableType extends AbstractTableType
{
    public function buildTable(TableBuilder $builder, array $options): void
    {
    }
}
