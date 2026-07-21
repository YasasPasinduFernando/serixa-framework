<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\ActivityItem;
use Serixa\Component\ActivityList;
use Serixa\Component\Avatar;
use Serixa\Component\Button;
use Serixa\Component\DataTable;
use Serixa\Component\EmptyState;
use Serixa\Component\Image;
use Serixa\Component\Link;
use Serixa\Component\Logo;
use Serixa\Component\Metric;
use Serixa\Component\PageHeader;
use Serixa\Component\Pagination;
use Serixa\Component\SearchField;
use Serixa\Component\StatCard;
use Serixa\Component\Table;
use Serixa\Component\TableBody;
use Serixa\Component\TableCell;
use Serixa\Component\TableHead;
use Serixa\Component\TableHeaderCell;
use Serixa\Component\TableRow;
use Serixa\Component\Toolbar;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Registry\WidgetRegistry;
use Serixa\Theme\ThemeManager;

final class DataDisplayTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testTablePrimitivesRenderSemanticMarkup(): void
    {
        $html = Table::make(
            TableHead::make(TableRow::make(TableHeaderCell::make('Name'))),
            TableBody::make(TableRow::make(TableCell::make('Ada'))),
        )->render();

        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('<thead', $html);
        $this->assertStringContainsString('<th', $html);
        $this->assertStringContainsString('scope="col"', $html);
        $this->assertStringContainsString('<td', $html);
        $this->assertStringContainsString('Ada', $html);
    }

    public function testDataTableSupportsMixedCellsAndEmptyState(): void
    {
        $table = DataTable::make()
            ->caption('Users')
            ->headers(['Name', 'Actions'])
            ->rows([
                ['Ada', Link::make('Edit')->href('/edit')],
            ])
            ->striped()
            ->compact()
            ->render();

        $this->assertStringContainsString('<table', $table);
        $this->assertStringContainsString('Ada', $table);
        $this->assertStringContainsString('href="/edit"', $table);
        $this->assertStringContainsString('<caption', $table);

        $empty = DataTable::make()
            ->headers(['Name'])
            ->rows([])
            ->emptyState(EmptyState::make()->title('No rows'))
            ->render();

        $this->assertStringContainsString('No rows', $empty);
        $this->assertStringNotContainsString('<table', $empty);
    }

    public function testDataTableSupportsCompositionApi(): void
    {
        $html = DataTable::make()
            ->striped()
            ->hover()
            ->child(
                Table::make()
                    ->head(TableRow::make(TableHeaderCell::make('Name'), TableHeaderCell::make('Role')))
                    ->body(
                        TableRow::make(TableCell::make('Ada'), TableCell::make('Admin')),
                        TableRow::make(TableCell::make('Alan'), TableCell::make(Link::make('Edit')->href('/edit'))),
                    ),
            )
            ->render();

        $this->assertStringContainsString('role="region"', $html);
        $this->assertStringContainsString('<thead', $html);
        $this->assertStringContainsString('<tbody', $html);
        $this->assertStringContainsString('Ada', $html);
        $this->assertStringContainsString('href="/edit"', $html);
    }

    public function testDataTableChildRequiresTable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DataTable::make()->child(Link::make('Nope')->href('#'));
    }

    public function testSearchFieldAndPagination(): void
    {
        $search = SearchField::make()->name('q')->placeholder('Find users')->value('ada')->render();
        $this->assertStringContainsString('type="search"', $search);
        $this->assertStringContainsString('role="searchbox"', $search);
        $this->assertStringContainsString('value="ada"', $search);

        $pager = Pagination::make()
            ->currentPage(2)
            ->totalPages(5)
            ->urlForPage(static fn (int $page): string => '/?p=' . $page)
            ->render();

        $this->assertStringContainsString('aria-label="Pagination"', $pager);
        $this->assertStringContainsString('href="/?p=1"', $pager);
        $this->assertStringContainsString('aria-current="page"', $pager);
    }

    public function testPaginationRequiresUrlCallback(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Pagination::make()->currentPage(1)->totalPages(3)->render();
    }

    public function testAvatarImageAndInitialsFallback(): void
    {
        $withImage = Avatar::make()->image('/a.png', 'Ada')->size('lg')->circle()->render();
        $this->assertStringContainsString('<img', $withImage);
        $this->assertStringContainsString('src="/a.png"', $withImage);

        $initials = Avatar::make()->initials('al')->square()->render();
        $this->assertStringContainsString('AL', $initials);
        $this->assertStringContainsString('rounded-md', $initials);
    }

    public function testImageAndLogo(): void
    {
        $img = Image::make()->src('/hero.jpg')->alt('Hero')->width(100)->height(50)->loading('eager')->render();
        $this->assertStringContainsString('src="/hero.jpg"', $img);
        $this->assertStringContainsString('alt="Hero"', $img);
        $this->assertStringContainsString('loading="eager"', $img);

        $logo = Logo::make()->text('SERIXA')->href('/')->render();
        $this->assertStringContainsString('<a', $logo);
        $this->assertStringContainsString('SERIXA', $logo);
        $this->assertStringContainsString('href="/"', $logo);
    }

    public function testStatCardMetricEmptyActivityPageHeaderToolbar(): void
    {
        $stat = StatCard::make()->title('Users')->value('1,284')->subtitle('7d')->trend('+4%')->color('primary')->render();
        $this->assertStringContainsString('Users', $stat);
        $this->assertStringContainsString('1,284', $stat);
        $this->assertStringContainsString('+4%', $stat);

        $metric = Metric::make()->label('Latency')->value('42ms')->render();
        $this->assertStringContainsString('<dl', $metric);
        $this->assertStringContainsString('Latency', $metric);

        $empty = EmptyState::make()->title('None')->description('Try again')->action(Button::make('Retry'))->render();
        $this->assertStringContainsString('None', $empty);
        $this->assertStringContainsString('Retry', $empty);

        $activity = ActivityList::make(
            ActivityItem::make()->title('Joined')->description('Ada')->timestamp('2h ago')->icon('•'),
        )->render();
        $this->assertStringContainsString('role="list"', $activity);
        $this->assertStringContainsString('Joined', $activity);
        $this->assertStringContainsString('<time', $activity);

        $header = PageHeader::make()->title('Users')->subtitle('Manage access')->actions(Button::make('Create')->primary())->render();
        $this->assertStringContainsString('<h1', $header);
        $this->assertStringContainsString('Create', $header);

        $toolbar = Toolbar::make()
            ->leading(SearchField::make()->name('q'))
            ->trailing(Button::make('New')->primary())
            ->render();
        $this->assertStringContainsString('type="search"', $toolbar);
        $this->assertStringContainsString('New', $toolbar);
    }

    public function testRegistryContainsDataDisplayWidgets(): void
    {
        $registry = WidgetRegistry::withDefaults();

        foreach ([
            'dataTable', 'table', 'searchField', 'pagination', 'avatar', 'image', 'logo',
            'statCard', 'metric', 'emptyState', 'activityList', 'activityItem', 'pageHeader', 'toolbar',
        ] as $name) {
            $this->assertTrue($registry->has($name), "Missing: {$name}");
        }
    }
}
