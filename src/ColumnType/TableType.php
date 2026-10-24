<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\TableFactory;
use MichaelKaefer\Table\TableView;
use MichaelKaefer\Table\TableViewFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TableType extends AbstractColumnType
{
    public function __construct(private readonly TableFactory $tableFactory, private readonly TableViewFactory $tableViewFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('table_type')
            ->setDefault('table_options', [])
            ->setAllowedTypes('table_type', ['string'])
            ->setAllowedTypes('table_options', ['array']);
    }

    public function transformData(mixed $data, array $options): TableView
    {
        $table = $this->tableFactory->create($options['table_type'], $options['table_options']);

        return $this->tableViewFactory->create($table, $data);
    }
}
