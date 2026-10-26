<?php

namespace MichaelKaefer\Table\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTableType
{
    public function __construct(
        public array $options = [],
        /**
         * @var array<int, TableColumn>
         */
        public array $columns = [],
    ) {
    }
}
