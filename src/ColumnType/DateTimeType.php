<?php

namespace MichaelKaefer\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'format' => \DateTimeInterface::RFC3339,
            ])
            ->setAllowedTypes('format', 'string');
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function transformData(mixed $data, array $options): ?string
    {
        if (null === $data) {
            return null;
        }

        if (!$data instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException(sprintf('Could not build cell view for value of type "%s". Expected value of type "%s".', get_debug_type($data), \DateTimeInterface::class));
        }

        return $data->format($options['format']);
    }
}
