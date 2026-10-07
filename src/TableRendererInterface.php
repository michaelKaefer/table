<?php

namespace MichaelKaefer\Table;

interface TableRendererInterface
{
    public function render(TableView $table): string;
}
