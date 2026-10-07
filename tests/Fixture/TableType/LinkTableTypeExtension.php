<?php

namespace MichaelKaefer\Table\Tests\Fixture\TableType;

use MichaelKaefer\Table\TableBuilder;
use MichaelKaefer\Table\TableTypeExtensionInterface;
use MichaelKaefer\Table\Tests\Fixture\Book;
use MichaelKaefer\Table\Tests\Fixture\ColumnType\LinkType;
use MichaelKaefer\Table\Tests\Fixture\Page;

class LinkTableTypeExtension implements TableTypeExtensionInterface
{
    public static function getExtendedTypes(): iterable
    {
        yield Book::class;
        yield Page::class;
    }

    public function buildTable(TableBuilder $builder): void
    {
        $builder->add('link_from_extension', LinkType::class, [
            'link_attr' => [
                'target' => '_blank'
            ],
        ]);
    }
}
