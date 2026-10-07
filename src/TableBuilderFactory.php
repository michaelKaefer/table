<?php

namespace MichaelKaefer\Table;

use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;

final readonly class TableBuilderFactory
{
    public function __construct(private TableRegistry $tableRegistry)
    {
    }

    public function create(string $type, array $options = []): TableBuilder
    {
        if (is_subclass_of($type, TableTypeInterface::class)) {
            $builder = new TableBuilder($options);
            $this->tableRegistry->getTableType($type)
                ->buildTable($builder);
        } else {
            if (!class_exists($type)) {
                throw new \InvalidArgumentException(sprintf('Class "%s" does not exist', $type));
            }

            $reflectionClass = new \ReflectionClass($type);

            if ($attributes = $reflectionClass->getAttributes(AsTableType::class)) {
                $builder = $this->createBuilderFromAttribute($reflectionClass, $attributes[0], $options);
            } else {
                throw new \InvalidArgumentException(sprintf('Class "%s" does not implement "%s" nor does it have an attribute of type "%s"', $type, TableTypeInterface::class, AsTableType::class));
            }
        }

        foreach ($this->tableRegistry->getTableTypeExtensions($type) as $tableExtension) {
            $tableExtension->buildTable($builder);
        }

        return $builder;
    }

    private function createBuilderFromAttribute(\ReflectionClass $reflectionClass, \ReflectionAttribute $attribute, array $options): TableBuilder
    {
        /** @var AsTableType $asTableType */
        $asTableType = $attribute->newInstance();

        $builder = new TableBuilder($options + $asTableType->options);

        foreach ($reflectionClass->getProperties() as $property) {
            if (!$property->getAttributes(TableColumn::class)) {
                continue;
            }

            /** @var TableColumn $attribute */
            $attribute = $property->getAttributes(TableColumn::class)[0]->newInstance();

            $builder->addFromAttribute($property, $attribute);
        }

        return $builder;
    }
}
