<?php

namespace MichaelKaefer\Table;

final readonly class TableFactory
{
    public function __construct(private TableBuilderFactory $tableBuilderFactory)
    {
    }

    public function createBuilder(string $type, array $options = []): TableBuilder
    {
        return $this->tableBuilderFactory->create($type, $options);
    }

    public function create(string $type, array $options = []): TableConfig
    {
        return $this->createBuilder($type, $options)->getTable();
    }
}
