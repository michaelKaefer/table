<?php

namespace MichaelKaefer\Table;

final class TableConfig
{
    /**
     * @var array<int, ColumnConfig>
     */
    public private(set) array $columns = [];

    public function __construct(public private(set) readonly array $options)
    {
    }

    public function add(string $name, string $type, array $options): self
    {
        $this->columns[] = new ColumnConfig($name, $type, $options);

        return $this;
    }
}
