<?php

namespace MichaelKaefer\Table;

final class TableConfig
{
    /**
     * @var array<int, ColumnConfig>
     */
    private(set) array $columns = [];

    public function __construct(private(set) readonly array $options)
    {
    }

    public function add(string $name, string $type, array $options): TableConfig
    {
        $this->columns[] = new ColumnConfig($name, $type, $options);

        return $this;
    }
}
