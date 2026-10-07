<?php

namespace MichaelKaefer\Table\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class TableColumn
{
    /**
     * @param class-string|null    $type    The column type, guessed when null
     * @param array<string, mixed> $options The options of the column
     * @param string|null          $name    The name of the column, defaults to the property name
     */
    public function __construct(
        public ?string $type = null,
        public array $options = [],
        public ?string $name = null,
    ) {
    }
}
