<?php

namespace MichaelKaefer\Table;

use Symfony\Component\PropertyAccess\PropertyPathInterface;

interface AccessorInterface
{
    public function getValue(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): mixed;
    public function isReadable(object|array $objectOrArray, ?PropertyPathInterface $propertyPath, ?callable $getter): bool;
}
