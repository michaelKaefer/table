<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'format' => \DateTimeInterface::RFC3339,
            ])
            ->setAllowedTypes('format', 'string');
    }

    public function buildView(CellView $view, array $options): void
    {
        $value = $view->vars['value'];

        if (null === $value) {
            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException(sprintf('Could not build cell view for value of type "%s". Expected value of type "%s".', get_debug_type($value), \DateTimeInterface::class));
        }

        $view->vars['value'] = $view->vars['value']->format($options['format']);
    }
}
