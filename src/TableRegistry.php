<?php

namespace MichaelKaefer\Table;

use Psr\Container\ContainerInterface;

final class TableRegistry
{
    /**
     * @var array<class-string, ResolvedTableType>
     */
    private array $resolvedTableTypes = [];

    /**
     * @var array<class-string, ResolvedColumnType>
     */
    private array $resolvedColumnTypes = [];

    public function __construct(private readonly ContainerInterface $tableTypes, private readonly iterable $tableTypeExtensions, private readonly ContainerInterface $columnTypes, private readonly iterable $columnTypeExtensions)
    {
    }

    public function getTableType(string $class, array $checked = []): ResolvedTableType
    {
        if (isset($checked[$class])) {
            $types = implode(' > ', [...array_keys($checked), $class]);
            throw new \LogicException(sprintf('Circular reference detected for table type "%s".', $types));
        }

        if (isset($this->resolvedTableTypes[$class])) {
            return $this->resolvedTableTypes[$class];
        }

        $checked[$class] = true;

        if (!$this->tableTypes->has($class)) {
            throw new \InvalidArgumentException(sprintf('Table type "%s" could not be found', $class));
        }

        $type = $this->tableTypes->get($class);
        $parent = $type->getParent();

        return $this->resolvedTableTypes[$class] = new ResolvedTableType(
            $type,
            $parent ? $this->getTableType($parent, $checked) : null,
            $this->getTableTypeExtensions($class),
        );
    }

    private function getTableTypeExtensions(string $type): array
    {
        $extensions = [];

        foreach ($this->tableTypeExtensions as $extension) {
            foreach ($extension->getExtendedTypes() as $extendedType) {
                if ($extendedType === $type) {
                    $extensions[] = $extension;
                }
            }
        }

        return $extensions;
    }

    public function getColumnType(string $class, array $checked = []): ResolvedColumnType
    {
        if (isset($checked[$class])) {
            $types = implode(' > ', array_merge(array_keys($checked), [$class]));
            throw new \LogicException(sprintf('Circular reference detected for column type "%s".', $types));
        }

        if (isset($this->resolvedColumnTypes[$class])) {
            return $this->resolvedColumnTypes[$class];
        }

        $checked[$class] = true;

        if (!$this->columnTypes->has($class)) {
            throw new \InvalidArgumentException(sprintf('Column type "%s" could not be found', $class));
        }

        $type = $this->columnTypes->get($class);
        $parent = $type->getParent();

        return $this->resolvedColumnTypes[$class] = new ResolvedColumnType(
            $type,
            $parent ? $this->getColumnType($parent, $checked) : null,
            $this->getColumnTypeExtensions($class),
        );
    }

    private function getColumnTypeExtensions(string $columnType): array
    {
        $extensions = [];

        foreach ($this->columnTypeExtensions as $extension) {
            foreach ($extension::getExtendedTypes() as $extendedType) {
                if ($extendedType === $columnType) {
                    $extensions[] = $extension;
                }
            }
        }

        return $extensions;
    }
}
