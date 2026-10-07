<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractColumnTypeExtension implements ColumnTypeExtensionInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function buildView(CellView $view, array $options): void
    {
    }
}
