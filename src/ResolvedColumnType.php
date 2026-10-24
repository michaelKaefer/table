<?php

namespace MichaelKaefer\Table;

final class ResolvedColumnType
{
    /**
     * @var array<int, ResolvedColumnType>
     */
    private array $parents;

    /**
     * @param array<ColumnTypeExtensionInterface> $extensions
     */
    public function __construct(private readonly ColumnTypeInterface $innerType, ?ResolvedColumnType $parent, private readonly array $extensions)
    {
        $this->parents = $parent !== null ? [$parent, ...$parent->getParents()] : [];
    }

    public function getInnerType(): ColumnTypeInterface
    {
        return $this->innerType;
    }

    /**
     * @return array<int, ResolvedColumnType>
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
