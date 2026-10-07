<?php

namespace MichaelKaefer\Table\Twig;

use MichaelKaefer\Table\TableRendererInterface;
use MichaelKaefer\Table\TableView;
use Twig\BlockChain;
use Twig\Environment;

final readonly class TwigTableRenderer implements TableRendererInterface
{
    public function __construct(private Environment $twig, private array $themes = [])
    {
    }

    public function render(TableView $table): string
    {
        return new BlockChain($this->twig, $this->themes)->renderBlock('table', $table->vars);
    }
}
