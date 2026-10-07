<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final readonly class CellViewFactory
{
    public function __construct(private AccessorInterface $accessor, private TableRegistry $tableRegistry)
    {
    }

    public function create(string|int $name, string $columnType, mixed $value, array $options): CellView
    {
        $columnType = $this->tableRegistry->getColumnType($columnType);

        $options = $this->resolve($columnType, $options);

        if (is_object($value) || is_array($value)) {
            $propertyPath = $this->propertyPath($name, $options);

            if ($this->accessor->isReadable($value, $propertyPath, $options['getter'])) {
                $value = $this->accessor->getValue($value, $propertyPath, $options['getter']);
            }
        }

        $cellView = new CellView([
            'value' => $value,
            'head' => $options['head'],
            'attr' => $options['attr'],
            'block_prefixes' => $this->blockPrefixes($columnType),
        ]);

        $this->buildView($columnType, $cellView, $options);

        return $cellView;
    }

    public function createHeadOrFoot(ColumnConfig $column): CellView
    {
        return new CellView([
            'value' => $column->name,
            'head' => true,
            'block_prefixes' => ['text'],
        ]);
    }

    private function propertyPath(string $name, array $options): ?PropertyPathInterface
    {
        if ($options['property_path']) {
            return $options['property_path'];
        }

        if (false === $options['property_path']) {
            return null;
        }

        return new PropertyPath($name);
    }

    private function blockPrefixes(ResolvedColumnType $type): array
    {
        $blockPrefixes = [];

        foreach ([$type, ...$type->getParents()] as $type) {
            $blockPrefix = $type->getInnerType()->getBlockPrefix();
            if ($blockPrefix) {
                $blockPrefixes[] = $blockPrefix;
            }
        }

        return $blockPrefixes;
    }

    public function resolve(ResolvedColumnType $type, array $options): array
    {
        $resolver = new OptionsResolver()
            ->setDefaults([
                'head' => false,
                'attr' => [],
                'property_path' => null,
                'getter' => null,
            ])
            ->setAllowedTypes('head', ['boolean'])
            ->setAllowedTypes('attr', 'array')
            ->setAllowedTypes('property_path', ['boolean', 'null', PropertyPathInterface::class])
            ->setAllowedTypes('getter', ['null', 'callable']);

        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $type->getInnerType()->configureOptions($resolver);
            foreach ($this->tableRegistry->getColumnTypeExtensions($type->getInnerType()::class) as $columnTypeExtension) {
                $columnTypeExtension->configureOptions($resolver);
            }
        }

        return $resolver->resolve($options);
    }

    private function buildView(ResolvedColumnType $type, CellView $cellView, array $options): void
    {
        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $type->getInnerType()->buildView($cellView, $options);

            foreach ($this->tableRegistry->getColumnTypeExtensions($type->getInnerType()::class) as $columnTypeExtension) {
                $columnTypeExtension->buildView($cellView, $options);
            }
        }
    }
}
