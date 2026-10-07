<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
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

    public function buildView(CellView $view, array $options): void
    {
        $view->vars['value'] = $view->vars['value'] ? $options['value'] : $options['false_value'];
    }
}
