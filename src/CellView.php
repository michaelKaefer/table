<?php

namespace MichaelKaefer\Table;

final class CellView
{
    /**
     * @var array{
     *     value: mixed,
     *     attr: array,
     *     head: bool,
     *     block_prefixes: array<int, string>,
     * }
     */
    public array $vars = [
        'value' => null,
        'attr' => [],
        'head' => false,
        'block_prefixes' => [],
    ];

    public function __construct(array $vars = [])
    {
        $this->vars = $vars + $this->vars;
    }
}
