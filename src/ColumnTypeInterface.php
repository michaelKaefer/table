<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ColumnTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void;
    public function getBlockPrefix(): ?string;
    public function getParent(): ?string;
    public function buildView(CellView $view, array $options): void;
}
