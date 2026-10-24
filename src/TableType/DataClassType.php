<?php

namespace MichaelKaefer\Table\TableType;

use MichaelKaefer\Table\AbstractTableType;
use MichaelKaefer\Table\TableBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DataClassType extends AbstractTableType
{
    public function __construct(public string $dataClass, private readonly array $columns, private readonly array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults($this->options + ['data_class' => $this->dataClass]);
    }

    public function buildTable(TableBuilder $builder, array $options): void
    {
        foreach ($this->columns as $name => [$type, $options]) {
            if (!isset($options['data_class'])) {
                $options['data_class'] = $this->dataClass;
            }

            $builder->add($name, $type, $options);
        }
    }
}
