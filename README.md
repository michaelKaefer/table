# Table

An extensible component for rendering tables.

## Installation

    composer require michaelkaefer/table

## Quick start

You can use attributes for any PHP object to configure the table:

```php
namespace App\Entity;

use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;
use MichaelKaefer\Table\ColumnType\BooleanType;
use MichaelKaefer\Table\ColumnType\DateTimeType;

#[AsTableType]
final class Book
{
    public function __construct(
        #[TableColumn]
        public int $id,

        #[TableColumn]
        public string $title,

        #[TableColumn(CollectionType::class)]
        private Collection $categories;
    ) {
    }
}
```

Available column types are:
- [BooleanType](src/ColumnType/BooleanType.php)
- [CollectionType](src/ColumnType/CollectionType.php) - Allows to combine multiple separate values in one column, for example multiple categories of a book
- [ColumnType](src/ColumnType/ColumnType.php) - The base of all other types
- [DateTimeType](src/ColumnType/DateTimeType.php)
- [TableType](src/ColumnType/TableType.php) - Allows to render another table in a column
- [TextType](src/ColumnType/TextType.php)

Then create a `TableView` object and pass it to Twig:

```php
namespace App\Controller;

use App\Entity\Book;
use MichaelKaefer\Table\TableFactory;
use MichaelKaefer\Table\TableViewFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class BooksController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TableFactory $tableFactory,
        private readonly TableViewFactory $tableViewFactory,
    ) {
    }

    public function __invoke(): Response
    {
        $iterable = $this->entityManager->getRepository(Book::class)
            ->createQueryBuilder('book')
            ->getQuery()
            ->toIterable();

        $tableConfig = $this->tableFactory->create(Book::class);
        
        $tableView = $this->tableViewFactory->create($tableConfig, $iterable);

        return $this->render('books.html.twig', [
            'table' => $tableView,
        ]);
    }
}
```

And finally render the table in `books.html.twig`:

```twig
{{ table(table) }}
```

The result will be:

```html
<table>
    <thead>
        <tr><th>id</th><th>title</th><th>categories</th></tr>
    </thead>
    <tbody>
        <tr><td>1</td><td>My first book</td><td>My first category, My second category</td></tr>
        <tr><td>2</td><td>My second book</td><td>My second category</td></tr>
    </tbody>
</table>
```

## Table types

Instead of using attributes to configure your table you can create a `TableType`:

```php
namespace App\Table\TableType;

use MichaelKaefer\Table\AbstractTableType;
use MichaelKaefer\Table\ColumnType\BooleanType;
use MichaelKaefer\Table\ColumnType\CollectionType;
use MichaelKaefer\Table\ColumnType\DateTimeType;
use MichaelKaefer\Table\ColumnType\TextType;
use MichaelKaefer\Table\TableBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BookTableType extends AbstractTableType
{
    public function buildTable(TableBuilder $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class)
            ->add('title', TextType::class)
            ->add('categories', CollectionType::class);
    }
}
```

And use it:

```php
$this->tableFactory->create(BookTableType::class);
```

## The iterable

The iterable can be passed to `TableViewFactory` in different shapes:

```php
// An iterable created by Doctrine
$iterable = $this->entityManager->getRepository(Book::class)
    ->createQueryBuilder('book')
    ->getQuery()
    ->toIterable();
$this->tableViewFactory->create($tableConfig, $iterable);

// An array of objects
$iterable = [
    new Book(1, 'Foo'),
    new Book(2, 'Bar'),
];
$this->tableViewFactory->create($tableConfig, $iterable);

// An array of arrays
$iterable = [
    ['id' => 1, 'name' => 'Foo'),
    ['id' => 2, 'name' => 'Bar'),
];
$this->tableViewFactory->create($tableConfig, $iterable);
```

## Creating a custom theme

The component provides three themes. Your app configures which should be used in `config/packages/michael_kaefer_table.yaml`:

```yaml
michael_kaefer_table:
    themes:
        - '@MichaelKaeferTable/table.html.twig'
        # Or one of the following
        # - '@MichaelKaeferTable/bootstrap_5.html.twig'
        # - '@MichaelKaeferTable/bootstrap_6.html.twig'
```

If you want to render your custom column types or overwrite base themes
you can create a custom table theme.

First create the Twig file, for example `templates/table/theme.html.twig`:

```twig
{# Overwrite the table block #}
{%- block table -%}
    ...
{%- endblock table -%}
```

Now add your theme in `config/packages/michael_kaefer_table.yaml`:

```yaml
michael_kaefer_table:
    themes:
        - 'table/theme.html.twig'
        # Fallback to the base theme
        - '@MichaelKaeferTable/table.html.twig'
```

## Creating a custom `ColumnType`

Imagine you want a column containing an HTML link to the author of a book. First create a `ColumnType`:

```php
namespace App\Table\ColumnType;

use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LinkType extends AbstractColumnType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                // Let's add a route option which allows to reuse this type
                'route' => 'app_author_show',
                // Allow to add HTML attributes to the HTML anchor tag
                'link_attr' => [],
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('link_attr', 'array');
    }

    public function transformData(mixed $data, array $options): array
    {
        // Let's assume your app defines a LinkableInterface containing the
        // methods getLinkId() and getLinkLabel()
        if (!$data instanceof LinkableInterface) {
            throw new \InvalidArgumentException();
        }
        
        $id = $data->getLinkId();
        $label = $data->getLinkLabel();
        
        return [
            'target' => $this->urlGenerator->generate($options['route'], ['id' => $id]),
            'label'  => $label,
        ];
    }

    public function buildView(CellView $view, array $options): void
    {
        $view->vars['link_attr'] = $options['link_attr'];
    }
}
```

Use it:

```php
namespace App\Entity;

use App\Table\ColumnType\LinkType;
use MichaelKaefer\Table\Attribute\AsTableType;
use MichaelKaefer\Table\Attribute\TableColumn;

#[AsTableType]
final class Book
{
    #[TableColumn(CollectionType::class, [
        'entry_type' => LinkType::class,
        'entry_options' => [
            'link_attr' => ['class' => 'my-class-name'],
        ],
    ])]
    private Collection $categories;
}
```

Finally add a Twig block named `link_cell` to your custom table theme:

```twig
{%- block link_cell -%}
    <a href="{{ value.target }}"{% with { attr: link_attr } %}{{ block('attributes') }}{% endwith %}>
        {{- value.label -}}
    </a>
{%- endblock link_cell -%}
```

## Extending table types

Table type extensions allow to add columns to one or multiple tables:

```php
use App\Table\TableType;

final class CreationDateExtension extends AbstractTableTypeExtension
{
    public function getExtendedTypes(): iterable
    {
        yield BookTableType::class;
        // Tables defined with #[AsTableType] are keyed by their class
        yield Page::class;
    }

    public function buildTable(TableBuilder $builder, array $options): void
    {
        $builder->add('createdAt', DateTimeType::class, ['format' => 'Y-m-d']);
    }
}
```

## Extending column types

Column types can be changed application wide by using column type extensions:

```php
namespace App\Table\TableType;

use MichaelKaefer\Table\AbstractColumnTypeExtension;
use MichaelKaefer\Table\ColumnType\BooleanType;

final class BooleanExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield BooleanType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['value' => 'Jap', 'false_value' => 'Nope']);
    }
}
```

## Inheritance

`TableTypes` as well as `ColumnTypes` can inherit options and behavior from base types:

```php
use MichaelKaefer\Table\AbstractTableType;
use MichaelKaefer\Table\ColumnType\DateTimeType;

final class DatedBookTableType extends AbstractTableType
{
    public function getParent(): ?string
    {
        return BookTableType::class;
    }

    public function buildTable(TableBuilder $builder, array $options): void
    {
        // Adds a column to the columns defined in the parent type
        $builder->add('publishedAt', DateTimeType::class, ['priority' => 100]);
    }
}
```

Imagine you only need to change the transformation and add an option to `TextType`:

```php
use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\ColumnType\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MoneyType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['currency' => 'EUR'])
            ->setAllowedTypes('currency', 'string');
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function transformData(mixed $data, array $options): string
    {
        return number_format($data / 100, 2, ',', '.').' '.$options['currency'];
    }
}
```

## Testing
    composer update
    ./vendor/bin/phpunit
