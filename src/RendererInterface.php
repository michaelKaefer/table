<?php

namespace MichaelKaefer\Table;

interface RendererInterface
{
    public function render(TableView $table): string;
}
