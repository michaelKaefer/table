<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\TableBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ColumnType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'head' => false,
                'attr' => [],
                'property_path' => null,
                'getter' => null,
                'priority' => 0,
                'data_class' => null,
                'data' => null,
                'transformer' => null,
            ])
            ->setAllowedTypes('label', ['string', 'null', TranslatableInterface::class])
            ->setAllowedTypes('head', 'boolean')
            ->setAllowedTypes('attr', 'array')
            ->setAllowedTypes('property_path', ['boolean', 'null', 'string', PropertyPathInterface::class])
            ->setAllowedTypes('getter', ['null', 'callable'])
            ->setAllowedTypes('priority', 'int')
            ->setAllowedTypes('data_class', ['string', 'null'])
            ->setAllowedTypes('transformer', ['null', 'callable'])
            ->setNormalizer('property_path', static function (Options $options, $value) {
                if (is_string($value)) {
                    return new PropertyPath($value);
                }

                return $value;
            })
            ->setInfo('priority', 'The column rendering priority (higher priorities will be rendered first)');
    }

    public function getParent(): ?string
    {
        return null;
    }
}
