<?php

namespace MichaelKaefer\Table;

final class ResolvedColumnType
{
    /**
     * @var array<int, ResolvedColumnType>
     */
    private array $parents = [];

    public function __construct(private readonly ColumnTypeInterface $innerType, private readonly ?ResolvedColumnType $parent = null)
    {
        $type = $this->parent;

        for (; $type !== null; $type = $type->getParent()) {
            $this->parents[] = $type;
        }
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
}
