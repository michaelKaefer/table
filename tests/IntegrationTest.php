<?php

namespace MichaelKaefer\Table\Tests;

use MichaelKaefer\Table\AbstractTableTypeExtension;
use MichaelKaefer\Table\Accessor\CallbackAccessor;
use MichaelKaefer\Table\Accessor\ChainAccessor;
use MichaelKaefer\Table\Accessor\PropertyPathAccessor;
use MichaelKaefer\Table\CellViewFactory;
use MichaelKaefer\Table\ColumnType\BooleanType;
use MichaelKaefer\Table\ColumnType\ColumnType;
use MichaelKaefer\Table\ColumnType\DateTimeType;
use MichaelKaefer\Table\TableBuilder;
use MichaelKaefer\Table\TableBuilderFactory;
use MichaelKaefer\Table\TableConfig;
use MichaelKaefer\Table\TableFactory;
use MichaelKaefer\Table\TableRegistry;
use MichaelKaefer\Table\TableType\DataClassType;
use MichaelKaefer\Table\TableTypeExtensionInterface;
use MichaelKaefer\Table\TableViewFactory;
use MichaelKaefer\Table\Tests\Fixture\Event;
use MichaelKaefer\Table\Tests\Fixture\EventRegistration;
use MichaelKaefer\Table\Tests\Fixture\Page;
use MichaelKaefer\Table\Tests\Fixture\Person;
use MichaelKaefer\Table\Tests\Fixture\PersonCountry;
use MichaelKaefer\Table\Tests\Fixture\PersonCountryCategory;
use MichaelKaefer\Table\Tests\Fixture\PersonCountryTag;
use MichaelKaefer\Table\Tests\Fixture\Room;
use MichaelKaefer\Table\Tests\Fixture\RoomDesk;
use MichaelKaefer\Table\Tests\Fixture\RoomDeskScreen;
use MichaelKaefer\Table\ColumnType\CollectionType;
use MichaelKaefer\Table\ColumnType\TableType;
use MichaelKaefer\Table\ColumnType\TextType;
use MichaelKaefer\Table\Tests\Fixture\ColumnType\LinkType;
use MichaelKaefer\Table\Tests\Fixture\TableType\EmptyTableType;
use MichaelKaefer\Table\Tests\Fixture\TableType\EventRegistrationsTableType;
use MichaelKaefer\Table\Tests\Fixture\TableType\PersonTableType;
use MichaelKaefer\Table\Renderer\TwigRenderer;
use phpDocumentor\Reflection\PseudoTypes\ArrayKey;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class IntegrationTest extends TestCase
{
    private TableFactory $tableFactory;
    private TableViewFactory $tableViewFactory;
    private TwigRenderer $twigRenderer;

    protected function setUp(): void
    {
        $cellViewFactory = $this->lazy($cellViewFactory, CellViewFactory::class);
        $tableFactory = $this->lazy($tableFactory, TableFactory::class);
        $tableViewFactory = $this->lazy($tableViewFactory, TableViewFactory::class);

        $tableRegistry = new TableRegistry(
            new class() implements ContainerInterface
            {
                private array $services;

                public function __construct()
                {
                    $this->services = [
                        \MichaelKaefer\Table\TableType\TableType::class => new \MichaelKaefer\Table\TableType\TableType(),
                        Fixture\TableType\EmptyTableType::class => new Fixture\TableType\EmptyTableType(),
                        Fixture\TableType\PersonTableType::class => new Fixture\TableType\PersonTableType(),
                        Fixture\TableType\EventRegistrationsTableType::class => new Fixture\TableType\EventRegistrationsTableType(),
                        Page::class => new DataClassType(Page::class, [
                            'id' => [TextType::class, []],
                            'name' => [TextType::class, []],
                            'pageCategories' => [CollectionType::class, []],
                        ], [])
                    ];
                }

                public function get(string $id)
                {
                    return $this->services[$id];
                }

                public function has(string $id): bool
                {
                    return isset($this->services[$id]);
                }
            },
            [
                new Fixture\TableType\LinkTableTypeExtension(),
                new class() extends AbstractTableTypeExtension {
                    public function getExtendedTypes(): iterable
                    {
                        yield Fixture\TableType\PersonTableType::class;
                    }

                    public function buildTable(TableBuilder $builder, array $options): void
                    {
                        $builder->add('country', TextType::class);
                    }
                },
            ],
            new class($cellViewFactory, $tableFactory, $tableViewFactory) implements ContainerInterface
            {
                private array $services;

                public function __construct($cellViewFactory, $tableFactory, $tableViewFactory)
                {
                    $this->services = [
                        ColumnType::class => new ColumnType(),
                        TextType::class => new TextType(),
                        BooleanType::class => new BooleanType(),
                        DateTimeType::class => new DateTimeType(),
                        CollectionType::class => new CollectionType($cellViewFactory),
                        TableType::class => new TableType($tableFactory, $tableViewFactory),
                        Fixture\ColumnType\LinkType::class => new Fixture\ColumnType\LinkType(),
                    ];
                }

                public function get(string $id)
                {
                    return $this->services[$id];
                }

                public function has(string $id): bool
                {
                    return isset($this->services[$id]);
                }
            },
            [
                new Fixture\ColumnType\BooleanTypeExtension(),
            ],
        );

        $cellViewFactory = new CellViewFactory(
            new ChainAccessor([
                new PropertyPathAccessor(PropertyAccess::createPropertyAccessor()),
                new CallbackAccessor(),
            ]),
            $tableRegistry,
        );

        $this->tableFactory = $tableFactory = new TableFactory(new TableBuilderFactory($tableRegistry));

        $this->tableViewFactory = $tableViewFactory = new TableViewFactory($cellViewFactory, $tableRegistry);

        $this->twigRenderer = new TwigRenderer(
            new Environment(
                new FilesystemLoader([
                    __DIR__.'/Fixture',
                    __DIR__.'/../templates/',
                ]),
                [
                    'strict_variables' => true,
                    'debug' => true,
                    'cache' => false,
                ],
            ),
            [
                'custom_theme_with_link_block.html.twig',
                'table.html.twig',
            ],
        );
    }

    public function testFromTableTypeAndTableExtension(): void
    {
        $table = $this->tableFactory->create(PersonTableType::class);

        $iterable = [
            new Person(1, 'Person 1', new PersonCountry(1, 'France')),
            new Person(2, 'Person 2', new PersonCountry(1, 'France')),
        ];

        $expected = <<<HTML
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>country</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Person 1</td>
                    <td>France</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Person 2</td>
                    <td>France</td>
                </tr>
            </tbody>
        </table>
        HTML;

        $this->assertTable($expected, $table, $iterable);
    }

    #[DataProvider('column')]
    public function testColumn(array $column, iterable $iterable, string $expected): void
    {
        $table = $this->tableFactory->create(EmptyTableType::class, ['head' => false])
            ->add(...$column);

        $expected = <<<HTML
        <table>
            <tbody>
                <tr>
                    $expected
                </tr>
            </tbody>
        </table>
        HTML;

        $this->assertTable($expected, $table, $iterable);
    }

    public static function column(): \Generator
    {
        yield [
            ['id', TextType::class, []],
            [
                new Person(),
            ],
            '<td></td>',
        ];
        yield [
            ['[id]', TextType::class, []],
            [
                [],
            ],
            '<td></td>',
        ];
        yield [
            ['id', TextType::class, []],
            [
                new Person(1),
            ],
            '<td>1</td>',
        ];
        yield [
            ['[id]', TextType::class, []],
            [
                ['id' => 1],
            ],
            '<td>1</td>',
        ];
        yield [
            ['active', BooleanType::class, []],
            [
                new Person(active: true),
            ],
            '<td>Yes</td>',
        ];
        yield [
            ['[active]', BooleanType::class, []],
            [
                ['active' => true],
            ],
            '<td>Yes</td>',
        ];
        yield [
            ['active', BooleanType::class, []],
            [
                new Person(active: false),
            ],
            '<td>No</td>',
        ];
        yield [
            ['active', BooleanType::class, ['value' => 'active']],
            [
                new Person(active: true),
            ],
            '<td>active</td>',
        ];
        yield [
            ['active', BooleanType::class, ['false_value' => 'inactive']],
            [
                new Person(active: false),
            ],
            '<td>inactive</td>',
        ];
        yield [
            ['createdAt', DateTimeType::class, []],
            [
                new Person(createdAt: \DateTimeImmutable::createFromFormat(
                    \DateTimeInterface::RFC3339_EXTENDED,
                    '2013-10-14T09:00:00.000+02:00',
                )),
            ],
            '<td>2013-10-14T09:00:00+02:00</td>',
        ];
        yield [
            ['name', TextType::class, []],
            [
                new Person(),
            ],
            '<td></td>',
        ];
        yield [
            ['[name]', TextType::class, []],
            [
                [],
            ],
            '<td></td>',
        ];
        yield [
            ['name', TextType::class, []],
            [
                new Person(name: 'foo'),
            ],
            '<td>foo</td>',
        ];
        yield [
            ['[name]', TextType::class, []],
            [
                ['name' => 'foo'],
            ],
            '<td>foo</td>',
        ];
        yield [
            ['country', TextType::class, []],
            [
                new Person(country: new PersonCountry()),
            ],
            '<td></td>',
        ];
        yield [
            ['country', TextType::class, []],
            [
                new Person(country: new PersonCountry(name: 'foo')),
            ],
            '<td>foo</td>',
        ];
        yield [
            ['country.name', TextType::class, []],
            [
                new Person(country: new PersonCountry(name: 'foo')),
            ],
            '<td>foo</td>',
        ];
        yield [
            ['[country][name]', TextType::class, []],
            [
                ['country' => ['name' => 'foo']],
            ],
            '<td>foo</td>',
        ];
        yield [
            ['country.category', TextType::class, []],
            [
                new Person(country: new PersonCountry(category: new PersonCountryCategory(name: 'foo'))),
            ],
            '<td>foo</td>',
        ];
        yield [
            ['country.category.id', TextType::class, []],
            [
                new Person(country: new PersonCountry(category: new PersonCountryCategory(id: 1))),
            ],
            '<td>1</td>',
        ];
        yield [
            ['[country][category][id]', TextType::class, []],
            [
                ['country' => ['category' => ['id' => 1]]],
            ],
            '<td>1</td>',
        ];
        yield [
            ['country.tags', CollectionType::class, []],
            [
                new Person(country: new PersonCountry(tags: [])),
            ],
            '<td></td>',
        ];
        yield [
            ['country.tags', CollectionType::class, []],
            [
                new Person(country: new PersonCountry(tags: [
                    new PersonCountryTag(name: 'foo'),
                    new PersonCountryTag(name: 'bar'),
                ])),
            ],
            '<td>foo,&nbsp;bar</td>',
        ];
        yield [
            ['country.tags', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'property_path' => new PropertyPath('id'),
                ],
            ]],
            [
                new Person(country: new PersonCountry(tags: [
                    new PersonCountryTag(id: 1),
                    new PersonCountryTag(id: 2),
                ])),
            ],
            '<td>1,&nbsp;2</td>',
        ];
        yield [
            ['[country][tags]', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'property_path' => new PropertyPath('[id]'),
                ],
            ]],
            [
                ['country' => ['tags' => [['id' => 1], ['id' => 2]]]],
            ],
            '<td>1,&nbsp;2</td>',
        ];
        yield [
            ['desks', CollectionType::class, []],
            [
                new Room(desks: [new RoomDesk(name: 'desk1'), new RoomDesk(name: 'desk2')]),
            ],
            '<td>desk1,&nbsp;desk2</td>',
        ];
        yield [
            ['desks[1].screens', CollectionType::class, []],
            [
                new Room(desks: [
                    new RoomDesk(screens: [
                        new RoomDeskScreen(name: 'screen1'),
                        new RoomDeskScreen(name: 'screen2'),
                    ]),
                    new RoomDesk(screens: [
                        new RoomDeskScreen(name: 'screen3'),
                        new RoomDeskScreen(name: 'screen4'),
                    ]),
                ]),
            ],
            '<td>screen3,&nbsp;screen4</td>',
        ];
        yield [
            ['desks[1].screens', CollectionType::class, ['entry_type' => LinkType::class]],
            [
                new Room(desks: [
                    new RoomDesk(screens: [
                        new RoomDeskScreen(1, 'screen1'),
                        new RoomDeskScreen(2, 'screen2'),
                    ]),
                    new RoomDesk(screens: [
                        new RoomDeskScreen(3, 'screen3'),
                        new RoomDeskScreen(4, 'screen4'),
                    ]),
                ]),
            ],
            '<td><a href="book/3">screen3</a>,&nbsp;<a href="book/4">screen4</a></td>',
        ];
        yield [
            ['desks', CollectionType::class, [
                'entry_type' => CollectionType::class,
                'entry_options' => [
                    'property_path' => new PropertyPath('screens'),
                    'entry_type' => TextType::class,
                ],
            ]],
            [
                new Room(desks: [
                    new RoomDesk(screens: [
                        new RoomDeskScreen(1, 'screen1'),
                        new RoomDeskScreen(2, 'screen2'),
                    ]),
                    new RoomDesk(screens: [
                        new RoomDeskScreen(3, 'screen3'),
                        new RoomDeskScreen(4, 'screen4'),
                    ]),
                ]),
            ],
            '<td>screen1,&nbsp;screen2,&nbsp;screen3,&nbsp;screen4</td>',
        ];
        yield [
            ['registrations', TableType::class, ['table_type' => EventRegistrationsTableType::class]],
            [
                new Event(registrations: [
                    new EventRegistration(1, 'registration1'),
                    new EventRegistration(2, 'registration2'),
                ]),
            ],
            self::minifyHtml(<<<HTML
            <td>
                <table>
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>registration1</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>registration2</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            HTML),
        ];
    }

    public function testAsTableType(): void
    {
        $table = $this->tableFactory->create(Page::class);

        $iterable = [];

        $expected = <<<HTML
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>pageCategories</th>
                    <th>link_from_extension</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        HTML;

        $this->assertTable($expected, $table, $iterable);
    }

    public function testAttr(): void
    {
        $table = $this->tableFactory->create(Page::class, [
            'attr' => ['class' => 'attr'],
            'head_attr' => ['class' => 'head_attr'],
            'body_attr' => ['class' => 'body_attr'],
            'foot_attr' => ['class' => 'foot_attr'],
        ]);

        $iterable = [];

        $expected = <<<HTML
        <table class="attr">
            <thead class="head_attr">
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>pageCategories</th>
                    <th>link_from_extension</th>
                </tr>
            </thead>
            <tbody class="body_attr">
            </tbody>
        </table>
        HTML;

        $this->assertTable($expected, $table, $iterable);
    }

    private function assertTable(string $expectedHtml, TableConfig $table, iterable $iterable): void
    {
        $tableView = $this->tableViewFactory->create($table, $iterable);

        $html = $this->twigRenderer->render($tableView);

        $this->assertSame(self::minifyHtml($expectedHtml), $html);
    }

    private static function minifyHtml(string $html): string
    {
        return preg_replace('/>\s+</', '><', $html);
    }

    private function lazy(null &$service, string $class): CellViewFactory|TableFactory|TableViewFactory
    {
        /**
         * @var CellViewFactory|TableFactory|TableViewFactory $service
         */
        $service = new \ReflectionClass($class)->newLazyProxy(
            function () use (&$service): CellViewFactory|TableFactory|TableViewFactory {
                return $service;
            }
        );

        return $service;
    }
}
