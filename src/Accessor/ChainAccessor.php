<?php

namespace MichaelKaefer\Table\Accessor;

use MichaelKaefer\Table\AccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final readonly class ChainAccessor implements AccessorInterface
{
    /**
     * @param iterable<AccessorInterface> $accessors
     */
    public function __construct(private iterable $accessors)
    {
    }

    public function getValue(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): mixed
    {
        foreach ($this->accessors as $accessor) {
            if ($accessor->isReadable($objectOrArray, $propertyPath, $getter)) {
                return $accessor->getValue($objectOrArray, $propertyPath, $getter);
            }
        }

        throw new \InvalidArgumentException('Unable to read from the given data as no accessor in the chain is able to read the data.');
    }

    public function isReadable(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): bool
    {
        foreach ($this->accessors as $accessor) {
            if ($accessor->isReadable($objectOrArray, $propertyPath, $getter)) {
                return true;
            }
        }

        return false;
    }
}
