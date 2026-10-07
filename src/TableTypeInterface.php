<?php

namespace MichaelKaefer\Table;

interface TableTypeInterface
{
    public function buildTable(TableBuilder $builder): void;
}