<?php

namespace MichaelKaefer\Table;

final class ResolvedTableType
{
    /**
     * @var array<int, ResolvedTableType>
     */
    private array $parents;

    /**
     * @param array<TableTypeExtensionInterface> $extensions
     */
    public function __construct(private readonly TableTypeInterface $innerType, ?ResolvedTableType $parent, private readonly array $extensions)
    {
        $this->parents = $parent !== null ? [$parent, ...$parent->getParents()] : [];
    }

    public function getInnerType(): TableTypeInterface
    {
        return $this->innerType;
    }

    /**
     * @return array<int, ResolvedTableType>
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
