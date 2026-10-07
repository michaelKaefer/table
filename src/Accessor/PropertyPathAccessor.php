<?php

namespace MichaelKaefer\Table\Accessor;

use MichaelKaefer\Table\AccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException as PropertyAccessException;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final readonly class PropertyPathAccessor implements AccessorInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    public function getValue(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): mixed
    {
        try {
            return $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
        } catch (PropertyAccessException $e) {
            if (\is_array($objectOrArray) && $e instanceof NoSuchIndexException) {
                return null;
            }

            if (!$e instanceof UninitializedPropertyException) {
                throw $e;
            }

            return null;
        }
    }

    public function isReadable(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): bool
    {
        if (null === $propertyPath) {
            return false;
        }

        return $this->propertyAccessor->isReadable($objectOrArray, $propertyPath);
    }
}
