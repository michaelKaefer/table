<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface TableTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void;
    public function getParent(): ?string;
    public function buildTable(TableBuilder $builder, array $options): void;
}
