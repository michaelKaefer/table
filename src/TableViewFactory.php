<?php

namespace MichaelKaefer\Table;

final readonly class TableViewFactory
{
    public function __construct(private CellViewFactory $cellViewFactory)
    {
    }

    public function create(TableConfig $table, iterable $iterable): TableView
    {
        $head = $table->options['head'] ? $this->createHeadOrFootRows($table->columns) : [];
        $body = $this->createBodyRows($table->columns, $iterable);
        $foot = $table->options['foot'] ? $this->createHeadOrFootRows($table->columns) : [];

        return new TableView([
            'attr' => $table->options['attr'],
            'head_attr' => $table->options['head_attr'],
            'body_attr' => $table->options['body_attr'],
            'foot_attr' => $table->options['foot_attr'],
            'head' => $this->sort($head),
            'body' => $this->sort($body),
            'foot' => $this->sort($foot),
        ]);
    }

    private function createHeadOrFootRows(array $columns): array
    {
        $row = [];

        foreach ($columns as $column) {
            $cell = $this->cellViewFactory->createHeadOrFoot($column->name, $column->type, $column->options);
            $row[$cell->vars['priority']][] = $cell;
        }

        return [$row];
    }

    private function createBodyRows(array $columns, iterable $iterable): array
    {
        $rows = [];

        foreach ($iterable as $item) {
            $row = [];

            foreach ($columns as $column) {
                $cell = $this->cellViewFactory->create($column->name, $column->type, $item, $column->options);
                $row[$cell->vars['priority']][] = $cell;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function sort(array $unsortedRows): array
    {
        $rows = [];

        foreach ($unsortedRows as $unsortedRow) {
            krsort($unsortedRow);

            $row = [];
            foreach ($unsortedRow as $cells) {
                $row = array_merge($row, $cells);
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
