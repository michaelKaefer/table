<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTableTypeExtension implements TableTypeExtensionInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function buildTable(TableBuilder $builder, array $options): void
    {
    }
}
