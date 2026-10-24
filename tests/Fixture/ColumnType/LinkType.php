<?php

namespace MichaelKaefer\Table\Tests\Fixture\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use MichaelKaefer\Table\Tests\Fixture\LinkableInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'link_attr' => [],
        ]);

        $resolver->setAllowedTypes('link_attr', ['array']);
    }

    public function getBlockPrefix(): string
    {
        return 'link';
    }

    public function transformData(mixed $data, array $options): mixed
    {
        if (!$data instanceof LinkableInterface) {
            throw new \InvalidArgumentException(sprintf('LinkType only supports type "%s" but got "%s".', LinkableInterface::class, get_debug_type($value)));
        }

        return ['target' => 'book/'.$data->getId(), 'label' => $data->getName()];
    }

    public function buildView(CellView $view, array $options): void
    {
        $view->vars['link_attr'] = $options['link_attr'];
    }
}
