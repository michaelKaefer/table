<?php

namespace MichaelKaefer\Table\Tests\Fixture\TableType;

use MichaelKaefer\Table\AbstractTableType;
use MichaelKaefer\Table\TableBuilder;
use MichaelKaefer\Table\ColumnType\TextType;

class PersonTableType extends AbstractTableType
{
    public function buildTable(TableBuilder $builder, array $options): void
    {
        $builder->add('id', TextType::class)
            ->add('name', TextType::class);
    }
}
