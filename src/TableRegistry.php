<?php

namespace MichaelKaefer\Table;

final class TableRegistry
{
    /**
     * @var array<class-string, ResolvedColumnType>
     */
    private array $resolvedColumnTypes = [];

    /**
     * @param iterable<TableTypeInterface>              $tableTypes
     * @param iterable<TableTypeExtensionInterface>     $tableTypeExtensions
     * @param iterable<ColumnTypeInterface>             $columnTypes
     * @param  iterable<ColumnTypeExtensionInterface>   $columnTypeExtensions
     */
    public function __construct(private readonly iterable $tableTypes, private readonly iterable $tableTypeExtensions, private readonly iterable $columnTypes, private readonly iterable $columnTypeExtensions)
    {
    }

    public function getTableType(string $class): TableTypeInterface
    {
        foreach ($this->tableTypes as $tableType) {
            if ($tableType::class === $class) {
                return $tableType;
            }
        }

        throw new \InvalidArgumentException(sprintf('Table type "%s" could not be found', $class));
    }

    public function getTableTypeExtensions(string $type): iterable
    {
        foreach ($this->tableTypeExtensions as $extension) {
            foreach ($extension::getExtendedTypes() as $extendedType) {
                if ($extendedType === $type) {
                    yield $extension;
                }
            }
        }
    }

    public function getColumnType(string $class, array $checkedTypes = []): ResolvedColumnType
    {
        if (isset($checkedTypes[$class])) {
            $types = implode(' > ', array_merge(array_keys($checkedTypes), [$class]));
            throw new \LogicException(sprintf('Circular reference detected for column type "%s".', $types));
        }

        if (isset($this->resolvedColumnTypes[$class])) {
            return $this->resolvedColumnTypes[$class];
        }

        $checkedTypes[$class] = true;

        foreach ($this->columnTypes as $type) {
            if ($type::class === $class) {
                $parent = $type->getParent();

                return $this->resolvedColumnTypes[$class] = new ResolvedColumnType(
                    $type,
                    $parent ? $this->getColumnType($parent, $checkedTypes) : null,
                );
            }
        }

        throw new \InvalidArgumentException(sprintf('Column type "%s" could not be found', $class));
    }

    public function getColumnTypeExtensions(string $columnType): iterable
    {
        foreach ($this->columnTypeExtensions as $extension) {
            foreach ($extension::getExtendedTypes() as $extendedType) {
                if ($extendedType === $columnType) {
                    yield $extension;
                }
            }
        }
    }
}
