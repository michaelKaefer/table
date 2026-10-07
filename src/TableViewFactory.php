<?php

namespace MichaelKaefer\Table;

final readonly class TableViewFactory
{
    public function __construct(private CellViewFactory $cellViewFactory)
    {
    }

    public function create(TableConfig $table, iterable $iterable): TableView
    {
        return new TableView([
            'attr' => $table->options['attr'],
            'head_attr' => $table->options['head_attr'],
            'body_attr' => $table->options['body_attr'],
            'foot_attr' => $table->options['foot_attr'],
            'head' => $table->options['head'] ? $this->createHeadOrFootRows($table) : [],
            'body' => $this->createBodyRows($table, $iterable),
            'foot' => $table->options['foot'] ? $this->createHeadOrFootRows($table) : [],
        ]);
    }

    private function createHeadOrFootRows(TableConfig $table): array
    {
        $row = [];

        foreach ($table->columns as $column) {
            $row[] = $this->cellViewFactory->createHeadOrFoot($column);
        }

        return [$row];
    }

    private function createBodyRows(TableConfig $table, iterable $iterable): array
    {
        $rows = [];

        foreach ($iterable as $item) {
            $row = [];

            foreach ($table->columns as $column) {
                $row[] = $this->cellViewFactory->create($column->name, $column->type, $item, $column->options);
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
