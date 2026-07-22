<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Serixa\Component\Accordion;
use Serixa\Component\AccordionItem;
use Serixa\Component\ActivityItem;
use Serixa\Component\ActivityList;
use Serixa\Component\Alert;
use Serixa\Component\AppBar;
use Serixa\Component\Avatar;
use Serixa\Component\Badge;
use Serixa\Component\Button;
use Serixa\Component\Card;
use Serixa\Component\Center;
use Serixa\Component\Checkbox;
use Serixa\Component\Chip;
use Serixa\Component\Column;
use Serixa\Component\Container;
use Serixa\Component\DataTable;
use Serixa\Component\Divider;
use Serixa\Component\Drawer;
use Serixa\Component\Dropdown;
use Serixa\Component\DropdownItem;
use Serixa\Component\DropdownMenu;
use Serixa\Component\EmptyState;
use Serixa\Component\ErrorText;
use Serixa\Component\Expanded;
use Serixa\Component\FieldGroup;
use Serixa\Component\Footer;
use Serixa\Component\Form;
use Serixa\Component\HelperText;
use Serixa\Component\Image;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\Link;
use Serixa\Component\Logo;
use Serixa\Component\MainContent;
use Serixa\Component\Metric;
use Serixa\Component\Modal;
use Serixa\Component\Padding;
use Serixa\Component\PageHeader;
use Serixa\Component\Pagination;
use Serixa\Component\PasswordInput;
use Serixa\Component\Popover;
use Serixa\Component\Progress;
use Serixa\Component\Radio;
use Serixa\Component\Row;
use Serixa\Component\Scaffold;
use Serixa\Component\SearchField;
use Serixa\Component\Select;
use Serixa\Component\Sidebar;
use Serixa\Component\SizedBox;
use Serixa\Component\Skeleton;
use Serixa\Component\Spacer;
use Serixa\Component\Spinner;
use Serixa\Component\Stack;
use Serixa\Component\StatCard;
use Serixa\Component\SwitchControl;
use Serixa\Component\Tab;
use Serixa\Component\Table;
use Serixa\Component\TableBody;
use Serixa\Component\TableCell;
use Serixa\Component\TableHead;
use Serixa\Component\TableHeaderCell;
use Serixa\Component\TableRow;
use Serixa\Component\TabPanel;
use Serixa\Component\Tabs;
use Serixa\Component\Text;
use Serixa\Component\Textarea;
use Serixa\Component\Toast;
use Serixa\Component\Toolbar;
use Serixa\Component\Tooltip;
use Serixa\Contracts\WidgetInterface;
use Serixa\Rendering\Renderer;
use Serixa\Theme\ThemeManager;

/**
 * Catalog smoke: every public widget renders without error and emits a stable marker.
 */
final class WidgetCatalogTest extends TestCase
{
    private Renderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = new Renderer();
    }

    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    /**
     * @return array<string, array{0: callable(): WidgetInterface, 1: list<string>}>
     */
    public static function catalogProvider(): array
    {
        $t = static fn (string $s): Text => Text::make($s);

        return [
            'Accordion' => [static fn () => Accordion::make()->item(
                AccordionItem::make()->title('A')->content($t('body')),
            ), ['data-serixa-accordion', 'A']],
            'ActivityList' => [static fn () => ActivityList::make(
                ActivityItem::make()->title('Event')->description('Desc'),
            ), ['Event', 'Desc']],
            'Alert' => [static fn () => Alert::make()->title('Note')->body('Hello'), ['Note', 'Hello']],
            'AppBar' => [static fn () => AppBar::make($t('Title')), ['Title']],
            'Avatar' => [static fn () => Avatar::make()->initials('SX'), ['SX']],
            'Badge' => [static fn () => Badge::make('3'), ['3']],
            'Button' => [static fn () => Button::make('Save')->primary(), ['sx-btn', 'Save']],
            'Card' => [static fn () => Card::make($t('Card')), ['Card']],
            'Center' => [static fn () => Center::make($t('C')), ['C']],
            'Checkbox' => [static fn () => Checkbox::make()->name('ok')->label('OK'), ['type="checkbox"', 'OK']],
            'Chip' => [static fn () => Chip::make('Tag'), ['Tag']],
            'Column' => [static fn () => Column::make($t('Col')), ['Col']],
            'Container' => [static fn () => Container::make($t('Box')), ['Box']],
            'DataTable' => [static fn () => DataTable::make()->headers(['A'])->rows([['1']]), ['<table', 'A', '1']],
            'Divider' => [static fn () => Divider::make(), ['hr', 'role="separator"']],
            'Drawer' => [static fn () => Drawer::make()->title('D')->body($t('body')), ['data-serixa-drawer', 'D']],
            'Dropdown' => [static fn () => Dropdown::make()
                ->trigger(Button::make('Menu'))
                ->items([DropdownItem::make('Item')]), ['data-serixa-dropdown', 'role="menu"']],
            'DropdownMenu' => [static fn () => DropdownMenu::make(DropdownItem::make('Edit')), ['role="menu"', 'Edit']],
            'EmptyState' => [static fn () => EmptyState::make()->title('Empty'), ['Empty']],
            'ErrorText' => [static fn () => ErrorText::make('Required'), ['Required']],
            'Expanded' => [static fn () => Expanded::make($t('E')), ['flex-1', 'E']],
            'FieldGroup' => [static fn () => FieldGroup::make(Label::make('Name'), Input::make()->name('n')), ['Name']],
            'Footer' => [static fn () => Footer::make($t('©')), ['©']],
            'Form' => [static fn () => Form::make(Input::make()->name('x')), ['<form']],
            'HelperText' => [static fn () => HelperText::make('Hint'), ['Hint']],
            'Image' => [static fn () => Image::make()->src('/a.png')->alt('Pic'), ['src="/a.png"', 'alt="Pic"']],
            'Input' => [static fn () => Input::make()->name('email')->type('email'), ['type="email"', 'name="email"']],
            'Label' => [static fn () => Label::make('Email'), ['Email']],
            'Link' => [static fn () => Link::make('Home')->href('/'), ['href="/"', 'Home']],
            'Logo' => [static fn () => Logo::make()->text('Serixa'), ['Serixa']],
            'MainContent' => [static fn () => MainContent::make($t('Main')), ['Main']],
            'Metric' => [static fn () => Metric::make()->label('Users')->value('12'), ['Users', '12']],
            'Modal' => [static fn () => Modal::make()->title('M')->body($t('body')), ['role="dialog"', 'M']],
            'Padding' => [static fn () => Padding::make($t('P')), ['P']],
            'PageHeader' => [static fn () => PageHeader::make()->title('Page'), ['Page']],
            'Pagination' => [static fn () => Pagination::make()
                ->currentPage(1)
                ->totalPages(3)
                ->urlForPage(static fn (int $p): string => '/?page=' . $p), ['1', '/?page=']],
            'PasswordInput' => [static fn () => PasswordInput::make()->name('pw'), ['type="password"']],
            'Popover' => [static fn () => Popover::make()
                ->trigger(Button::make('More'))
                ->content($t('Details')), ['data-serixa-popover', 'role="dialog"', 'Details']],
            'Progress' => [static fn () => Progress::make()->value(40), ['role="progressbar"', 'aria-valuenow="40"']],
            'Radio' => [static fn () => Radio::make()->name('g')->value('a')->label('A'), ['type="radio"', 'A']],
            'Row' => [static fn () => Row::make($t('R')), ['R']],
            'Scaffold' => [static fn () => Scaffold::make()->body($t('Shell')), ['Shell']],
            'SearchField' => [static fn () => SearchField::make()->name('q'), ['type="search"', 'name="q"']],
            'Select' => [static fn () => Select::make()->name('s')->options(['a' => 'A']), ['<select', 'A']],
            'Sidebar' => [static fn () => Sidebar::make($t('Nav')), ['Nav']],
            'SizedBox' => [static fn () => SizedBox::make($t('S')), ['S']],
            'Skeleton' => [static fn () => Skeleton::make()->lines(2), ['aria-hidden', 'role="status"']],
            'Spacer' => [static fn () => Spacer::make(), ['flex-1']],
            'Spinner' => [static fn () => Spinner::make(), ['role="status"']],
            'Stack' => [static fn () => Stack::make($t('St')), ['St']],
            'StatCard' => [static fn () => StatCard::make()->title('Rev')->value('$1'), ['Rev', '$1']],
            'SwitchControl' => [static fn () => SwitchControl::make()->name('on')->label('On'), ['On']],
            'TabPanel' => [static fn () => TabPanel::make()->content($t('Panel')), ['Panel']],
            'Tabs' => [static fn () => Tabs::make()
                ->tab(Tab::make('One')->content($t('P1'))->active()), ['role="tablist"', 'One', 'P1']],
            'Table' => [static fn () => Table::make(
                TableHead::make(TableRow::make(TableHeaderCell::make('H'))),
                TableBody::make(TableRow::make(TableCell::make('C'))),
            ), ['<table', 'H', 'C']],
            'Text' => [static fn () => Text::make('Hello'), ['Hello']],
            'Textarea' => [static fn () => Textarea::make()->name('bio'), ['<textarea', 'name="bio"']],
            'Toast' => [static fn () => Toast::make()->message('Saved'), ['Saved']],
            'Toolbar' => [static fn () => Toolbar::make()->trailing(Button::make('New')), ['New']],
            'Tooltip' => [static fn () => Tooltip::make()->text('Tip')->child(Button::make('?')), ['Tip', 'role="tooltip"']],
        ];
    }

    /**
     * @param callable(): WidgetInterface $factory
     * @param list<string> $needles
     */
    #[DataProvider('catalogProvider')]
    public function testWidgetCatalogRender(callable $factory, array $needles): void
    {
        $html = $this->renderer->render($factory());
        $this->assertNotSame('', $html);
        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $html);
        }
    }
}
