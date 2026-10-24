<?php

namespace MichaelKaefer\Table;

use MichaelKaefer\Table\TableType\DataClassType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final readonly class TableBuilderFactory
{
    public function __construct(private TableRegistry $tableRegistry)
    {
    }

    public function create(string $type, array $options = []): TableBuilder
    {
        $options = $this->resolve($this->tableRegistry->getTableType($type), $options);

        $builder = new TableBuilder($options);

        $this->buildTable($builder, $this->tableRegistry->getTableType($type), $options);

        return $builder;
    }

    private function resolve(ResolvedTableType $type, array $options): array
    {
        $resolver = new OptionsResolver();

        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $type->getInnerType()->configureOptions($resolver);
            foreach ($type->getExtensions() as $extension) {
                $extension->configureOptions($resolver);
            }
        }

        return $resolver->resolve($options);
    }

    private function buildTable(TableBuilder $builder, ResolvedTableType $type, array $options): void
    {
        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $innerType = $type->getInnerType();

            $innerType->buildTable($builder, $options);

            foreach ($type->getExtensions() as $extension) {
                $extension->buildTable($builder, $options);
            }

//            if ($innerType instanceof DataClassType) {
//                foreach ($type->getExtensions() as $extension) {
//                    $extension->buildTable($builder, $options);
//                }
//            }
        }
    }
}
