<?php

namespace MichaelKaefer\Table\Tests\Fixture\ColumnType;

use MichaelKaefer\Table\AbstractColumnTypeExtension;
use MichaelKaefer\Table\ColumnType\BooleanType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BooleanTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield BooleanType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'value' => 'Yes',
            'false_value' => 'No',
        ]);
    }
}
