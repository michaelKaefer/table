<?php

namespace MichaelKaefer\Table\DependencyInjection\Compiler;

use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;
use MichaelKaefer\Table\ColumnType\TextType;
use MichaelKaefer\Table\TableType\DataClassType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class AsTableTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach (array_keys($container->findTaggedResourceIds('table.as_table_type', false)) as $id) {
            $class = $container->getDefinition($id)->getClass();

            $reflector = $container->getReflectionClass($class);

            if (!$attributes = $reflector->getAttributes(AsTableType::class, \ReflectionAttribute::IS_INSTANCEOF)) {
                throw new InvalidArgumentException(\sprintf('The class "%s" is tagged "table.as_table_type" but has no #[AsTableType] attribute.', $class));
            }

            $asTableType = $attributes[0]->newInstance();
            $options = $asTableType->options;

            $columns = [];
            foreach ($reflector->getProperties() as $property) {
                if (!$property->getAttributes(TableColumn::class)) {
                    continue;
                }

                /** @var TableColumn $tableColumn */
                $tableColumn = $property->getAttributes(TableColumn::class)[0]->newInstance();

                if (isset($tableColumn->options['property_path'])) {
                    throw new \InvalidArgumentException(sprintf('Option "property_path" is not allowed in "%s".', TableColumn::class));
                }

                $columnOptions = ['property_path' => $property->getName()] + $tableColumn->options;

                $columns[$tableColumn->name ?? $property->getName()] = [
                    $tableColumn->type ?? TextType::class,
                    $columnOptions,
                ];
            }

            $definition = new Definition(DataClassType::class, [$class, $columns, $options]);
            $definition->addTag('table.table_type', ['key' => $class]);

            $container->setDefinition($id.'.table_type', $definition);
        }
    }
}
