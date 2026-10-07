<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ColumnTypeExtensionInterface
{
    /**
     * @return string[]
     */
    public static function getExtendedTypes(): iterable;

    public function configureOptions(OptionsResolver $resolver): void;

    public function buildView(CellView $view, array $options): void;
}
