<?php

namespace MichaelKaefer\Table\Tests\Fixture\TableType;

use MichaelKaefer\Table\AbstractTableType;
use MichaelKaefer\Table\ColumnType\TextType;
use MichaelKaefer\Table\TableBuilder;

class EventRegistrationsTableType extends AbstractTableType
{
    public function buildTable(TableBuilder $builder, array $options): void
    {
        $builder->add('id', TextType::class)
            ->add('name', TextType::class);
    }
}
