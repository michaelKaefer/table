<?php

namespace MichaelKaefer\Table\Renderer;

use MichaelKaefer\Table\RendererInterface;
use MichaelKaefer\Table\TableView;
use Twig\BlockChain;
use Twig\Environment;

final readonly class TwigRenderer implements RendererInterface
{
    public function __construct(private Environment $twig, private array $themes = [])
    {
    }

    public function render(TableView $table): string
    {
        return new BlockChain($this->twig, $this->themes)->renderBlock('table', $table->vars);
    }
}
