<?php

namespace MichaelKaefer\Table;

use MichaelKaefer\Table\TableType\TableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTableType implements TableTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getParent(): ?string
    {
        return TableType::class;
    }

    public function buildTable(TableBuilder $builder, array $options): void
    {
    }
}
