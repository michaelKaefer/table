<?php

namespace MichaelKaefer\Table\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTableType
{
    public function __construct(
        public array $options = [],
    ) {
    }
}
