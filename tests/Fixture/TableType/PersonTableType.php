<?php

namespace MichaelKaefer\Table\Tests\Fixture\TableType;

use MichaelKaefer\Table\TableBuilder;
use MichaelKaefer\Table\TableTypeInterface;
use MichaelKaefer\Table\ColumnType\TextType;

class PersonTableType implements TableTypeInterface
{
    public function buildTable(TableBuilder $builder): void
    {
        $builder->add('id', TextType::class)
            ->add('name', TextType::class);
    }
}
