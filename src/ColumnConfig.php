<?php

namespace MichaelKaefer\Table;

final readonly class ColumnConfig
{
    public function __construct(public string $name, public string $type, public array $options)
    {
    }
}
