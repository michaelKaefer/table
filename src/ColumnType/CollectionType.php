<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use MichaelKaefer\Table\CellViewFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CollectionType extends AbstractColumnType
{
    public function __construct(private readonly CellViewFactory $cellViewFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'entry_type' => TextType::class,
                'entry_options' => [],
            ])
            ->setAllowedTypes('entry_type', ['string'])
            ->setAllowedTypes('entry_options', ['array']);
    }

    public function buildView(CellView $view, array $options): void
    {
        $value = $view->vars['value'];

        if (!is_iterable($value)) {
            throw new \LogicException('The given value is not iterable.');
        }

        $newValue = [];

        $count = 0;
        foreach ($value as $v) {
            $newValue[] = $this->cellViewFactory->create(
                $count++,
                $options['entry_type'],
                $v,
                $options['entry_options'],
            );
        }

        $view->vars['value'] = $newValue;
    }
}
