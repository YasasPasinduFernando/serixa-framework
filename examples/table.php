<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Assets\StyleSheet;
use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\DataTable;
use Serixa\Component\EmptyState;
use Serixa\Component\Link;
use Serixa\Component\PageHeader;
use Serixa\Component\Pagination;
use Serixa\Component\SearchField;
use Serixa\Component\Table;
use Serixa\Component\TableBody;
use Serixa\Component\TableCell;
use Serixa\Component\TableHead;
use Serixa\Component\TableHeaderCell;
use Serixa\Component\TableRow;
use Serixa\Component\Text;
use Serixa\Component\Toolbar;
use Serixa\Document\Document;
use Serixa\Rendering\Renderer;

$convenience = DataTable::make()
    ->headers(['Name', 'Email', 'Actions'])
    ->rows([
        ['Ada Lovelace', 'ada@example.com', Link::make('Edit')->href('#')],
        ['Alan Turing', 'alan@example.com', Link::make('Edit')->href('#')],
    ])
    ->striped()
    ->emptyState(EmptyState::make()->title('No users'));

$composed = DataTable::make()
    ->hover()
    ->child(
        Table::make()
            ->head(
                TableHead::make(
                    TableRow::make(
                        TableHeaderCell::make('Name'),
                        TableHeaderCell::make('Role'),
                    ),
                ),
            )
            ->body(
                TableBody::make(
                    TableRow::make(TableCell::make('Grace'), TableCell::make('Viewer')),
                ),
            ),
    );

$page = Column::make([
    PageHeader::make()->title('Users')->actions(Button::make('Create')->primary()),
    Toolbar::make()
        ->leading(SearchField::make()->name('q')->placeholder('Search…'))
        ->trailing(Button::make('Export')->secondary()),
    Text::make('Convenience API')->as('h2')->size('lg')->weight('semibold'),
    $convenience,
    Text::make('Composition API')->as('h2')->size('lg')->weight('semibold'),
    $composed,
    Pagination::make()
        ->currentPage(1)
        ->totalPages(3)
        ->urlForPage(static fn (int $p): string => '/?p=' . $p),
])->gap(4);

$document = Document::make()->title('Table · SERIXA')->body($page);
StyleSheet::register($document->assets());

echo (new Renderer())->render($document), PHP_EOL;
