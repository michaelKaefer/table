<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
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

    public function transformData(mixed $data, array $options): array
    {
        if (!is_iterable($data)) {
            throw new \LogicException(sprintf('The given value is not iterable. Value is of type %s.', get_debug_type($data)));
        }

        $cellViews = [];
        $count = 0;
        foreach ($data as $d) {
            $cellViews[] = $this->cellViewFactory->create(
                $count++,
                $options['entry_type'],
                $d,
                $options['entry_options'],
            );
        }

        return $cellViews;
    }
}
