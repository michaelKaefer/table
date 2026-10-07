<?php

namespace MichaelKaefer\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

final readonly class TableFactory
{
    public function __construct(private TableBuilderFactory $tableBuilderFactory)
    {
    }

    public function create(string $type, array $options = []): TableConfig
    {
        $resolver = new OptionsResolver()
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

        $options = $resolver->resolve($options);

        return $this->tableBuilderFactory->create($type, $options)->getTable();
    }
}
