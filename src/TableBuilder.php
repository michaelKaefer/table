<?php

namespace MichaelKaefer\Table;

final class TableBuilder
{
    private TableConfig $table;

    public function __construct(array $options)
    {
        $this->table = new TableConfig($options);
    }

    public function add(string $name, string $type, array $options = []): TableBuilder
    {
        $this->table->add($name, $type, $options);

        return $this;
    }

    public function getTable(): TableConfig
    {
        return $this->table;
    }
}
