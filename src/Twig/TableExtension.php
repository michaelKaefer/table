<?php

namespace MichaelKaefer\Table\Twig;

use MichaelKaefer\Table\RendererInterface;
use MichaelKaefer\Table\TableView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TableExtension extends AbstractExtension
{
    public function __construct(private readonly RendererInterface $renderer)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('table', [$this, 'table'], ['is_safe' => ['html']]),
        ];
    }

    public function table(TableView $view): string
    {
        return $this->renderer->render($view);
    }
}
