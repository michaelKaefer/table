<?php

namespace MichaelKaefer\Table;

interface TableTypeExtensionInterface
{
    public static function getExtendedTypes(): iterable;
    public function buildTable(TableBuilder $builder): void;
}