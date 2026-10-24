<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface TableTypeExtensionInterface
{
    public function configureOptions(OptionsResolver $resolver): void;
    public function getExtendedTypes(): iterable;
    public function buildTable(TableBuilder $builder, array $options): void;
}