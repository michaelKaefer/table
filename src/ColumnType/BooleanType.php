<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BooleanType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'value' => 'y',
                'false_value' => 'n',
            ])
            ->setAllowedTypes('value', 'string')
            ->setAllowedTypes('false_value', 'string');
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function transformData(mixed $data, array $options): string
    {
        return $data ? $options['value'] : $options['false_value'];
    }
}
