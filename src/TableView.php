<?php

namespace MichaelKaefer\Table;

final class TableView
{
    /**
     * @var array{
     *     attr: array,
     *     head_attr: array,
     *     body_attr: array,
     *     foot_attr: array,
     *     head: array<int, array<int, CellView>>,
     *     body: array<int, array<int, CellView>>,
     *     foot: array<int, array<int, CellView>>,
     * }
     */
    public array $vars = [
        'attr' => [],
        'head_attr' => [],
        'body_attr' => [],
        'foot_attr' => [],
        'head' => [],
        'body' => [],
        'foot' => [],
    ];

    public function __construct(array $vars = [])
    {
        $this->vars = $vars + $this->vars;
    }
}
