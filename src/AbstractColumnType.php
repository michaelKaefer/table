<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\UnicodeString;

abstract class AbstractColumnType implements ColumnTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getBlockPrefix(): ?string
    {
        return new UnicodeString(new \ReflectionClass(static::class)->getShortName())
            ->replaceMatches('/Type$/', '')
            ->snake()
            ->toString();
    }

    public function getParent(): ?string
    {
        return null;
    }

    public function buildView(CellView $view, array $options): void
    {
    }
}
