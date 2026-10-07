<?php

namespace MichaelKaefer\Table\Accessor;

use MichaelKaefer\Table\AccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final readonly class CallbackAccessor implements AccessorInterface
{
    public function getValue(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): mixed
    {
        if (null === $getter) {
            throw new \InvalidArgumentException('Unable to read from the given data as no getter is defined.');
        }

        return ($getter)($objectOrArray);
    }

    public function isReadable(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): bool
    {
        return null !== $getter;
    }
}
