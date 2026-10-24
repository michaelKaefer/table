<?php

namespace MichaelKaefer\Table;

use MichaelKaefer\Table\ColumnType\ColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\UnicodeString;

abstract class AbstractColumnType implements ColumnTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getBlockPrefix(): string
    {
        return new UnicodeString(new \ReflectionClass(static::class)->getShortName())
            ->replaceMatches('/Type$/', '')
            ->snake()
            ->toString();
    }

    public function getParent(): ?string
    {
        return ColumnType::class;
    }

    public function transformData(mixed $data, array $options): mixed
    {
        return $data;
    }

    public function buildView(CellView $view, array $options): void
    {
    }
}
