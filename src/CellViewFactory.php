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

        $cellView = new CellView();

        $this->buildView($columnType, $cellView, $options);

        $cellView->vars['value'] = $this->transformData($columnType, $value, $options);
        $cellView->vars['head'] = $options['head'];
        $cellView->vars['attr'] = $options['attr'];
        $cellView->vars['priority'] = $options['priority'];
        $cellView->vars['block_prefixes'] = $this->blockPrefixes($columnType);

        return $cellView;
    }

    public function createHeadOrFoot(string|int $name, string $columnType, array $options): CellView
    {
        $columnType = $this->tableRegistry->getColumnType($columnType);

        $options = $this->resolve($columnType, $options);

        return new CellView([
            'value' => $options['label'] ?? $name,
            'head' => true,
            'priority' => $options['priority'],
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

    private function resolve(ResolvedColumnType $type, array $options): array
    {
        $resolver = new OptionsResolver();

        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $type->getInnerType()->configureOptions($resolver);
            foreach ($type->getExtensions() as $columnTypeExtension) {
                $columnTypeExtension->configureOptions($resolver);
            }
        }

        return $resolver->resolve($options);
    }

    private function transformData(ResolvedColumnType $type, mixed $value, array $options): mixed
    {
        if (null !== $options['data']) {
            $value = $options['data'];
        }

        if ($options['transformer'] ?? false) {
            $value = ($options['transformer'])($value);
        }

        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $value = $type->getInnerType()->transformData($value, $options);

            foreach ($type->getExtensions() as $columnTypeExtension) {
                $value = $columnTypeExtension->transformData($value, $options);
            }
        }

        return $value;
    }

    private function buildView(ResolvedColumnType $type, CellView $cellView, array $options): void
    {
        foreach (array_reverse([$type, ...$type->getParents()]) as $type) {
            $type->getInnerType()->buildView($cellView, $options);

            foreach ($type->getExtensions() as $columnTypeExtension) {
                $columnTypeExtension->buildView($cellView, $options);
            }
        }
    }
}
