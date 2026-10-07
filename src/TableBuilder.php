<?php

namespace MichaelKaefer\Table;

use MichaelKaefer\Table\Attribute\TableColumn;
use MichaelKaefer\Table\ColumnType\TextType;
use Symfony\Component\PropertyAccess\PropertyPath;

final class TableBuilder
{
    private TableConfig $table;

    public function __construct(array $options)
    {
        $this->table = new TableConfig($options);
    }

    public function addFromAttribute(\ReflectionProperty $property, TableColumn $attribute): TableBuilder
    {
        if (isset($attribute->options['property_path'])) {
            throw new \InvalidArgumentException(sprintf('Option "property_path" is not allowed in "%s".', TableColumn::class));
        }

        $options = ['property_path' => new PropertyPath($property->getName())] + $attribute->options;

        $this->table->add($attribute->name ?? $property->getName(), $attribute->type ?? TextType::class, $options);

        return $this;
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
