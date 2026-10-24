<?php

namespace MichaelKaefer\Table\TableType;

use MichaelKaefer\Table\AbstractTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TableType extends AbstractTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'head' => true,
                'foot' => false,
                'attr' => [],
                'head_attr' => [],
                'body_attr' => [],
                'foot_attr' => [],
            ])
            ->setAllowedTypes('head', ['boolean'])
            ->setAllowedTypes('foot', ['boolean'])
            ->setAllowedTypes('attr', ['array'])
            ->setAllowedTypes('head_attr', ['array'])
            ->setAllowedTypes('body_attr', ['array'])
            ->setAllowedTypes('foot_attr', ['array']);
    }

    public function getParent(): ?string
    {
        return null;
    }
}
