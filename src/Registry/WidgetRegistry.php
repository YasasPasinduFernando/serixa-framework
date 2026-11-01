<?php

declare(strict_types=1);

namespace Serixa\Registry;

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
use Serixa\Exception\RegistryException;

/**
 * Registers widget types and their metadata for discovery.
 */
final class WidgetRegistry
{
    /** @var array<string, WidgetMetadata> */
    private array $widgets = [];

    /** @var list<WidgetMetadata>|null */
    private static ?array $defaultMetadataCache = null;

    public function register(WidgetMetadata $metadata): void
    {
        if ($metadata->name() === '') {
            throw new RegistryException('Widget metadata name cannot be empty.');
        }

        $this->widgets[$metadata->name()] = $metadata;
    }

    public function unregister(string $name): void
    {
        unset($this->widgets[$name]);
    }

    public function find(string $name): ?WidgetMetadata
    {
        return $this->widgets[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->widgets[$name]);
    }

    /**
     * @return array<string, WidgetMetadata>
     */
    public function all(): array
    {
        return $this->widgets;
    }

    /**
     * @return class-string<WidgetInterface>|null
     */
    public function classFor(string $name): ?string
    {
        $meta = $this->find($name);

        if ($meta === null || $meta->className() === '') {
            return null;
        }

        /** @var class-string<WidgetInterface> $class */
        $class = $meta->className();

        return $class;
    }

    /**
     * Seed registry with built-in SERIXA widgets.
     */
    public static function withDefaults(): self
    {
        $registry = new self();

        foreach (self::defaultMetadata() as $metadata) {
            $registry->register($metadata);
        }

        return $registry;
    }

    /**
     * @return list<WidgetMetadata>
     */
    public static function defaultMetadata(): array
    {
        return self::$defaultMetadataCache ??= [
            new WidgetMetadata('button', 'input', 'Interactive button', ['variant', 'size', 'disabled', 'rounded'], ['click'], false, '0.1.0', Button::class),
            new WidgetMetadata('text', 'typography', 'Semantic text node', ['size', 'weight', 'tone', 'as'], [], false, '0.1.0', Text::class),
            new WidgetMetadata('card', 'surface', 'Content card', ['title', 'padding'], [], true, '0.1.0', Card::class),
            new WidgetMetadata('container', 'layout', 'Page width container', ['maxWidth', 'padding'], [], true, '0.1.0', Container::class),
            new WidgetMetadata('row', 'layout', 'Horizontal flex row', ['justify', 'align', 'gap'], [], true, '0.2.0', Row::class),
            new WidgetMetadata('column', 'layout', 'Vertical flex column', ['align', 'gap'], [], true, '0.2.0', Column::class),
            new WidgetMetadata('center', 'layout', 'Centers children', [], [], true, '0.2.0', Center::class),
            new WidgetMetadata('padding', 'layout', 'Padding wrapper', ['all', 'x', 'y'], [], true, '0.2.0', Padding::class),
            new WidgetMetadata('spacer', 'layout', 'Flex spacer', [], [], false, '0.2.0', Spacer::class),
            new WidgetMetadata('expanded', 'layout', 'Flex grow wrapper', [], [], true, '0.2.0', Expanded::class),
            new WidgetMetadata('sizedBox', 'layout', 'Fixed width/height box', ['width', 'height'], [], true, '0.2.0', SizedBox::class),
            new WidgetMetadata('stack', 'layout', 'Layered stack', [], [], true, '0.2.0', Stack::class),
            new WidgetMetadata('divider', 'layout', 'Separator rule', ['orientation', 'spacing', 'label'], [], false, '0.6.0', Divider::class),
            new WidgetMetadata('scaffold', 'layout', 'App shell slots', ['appBar', 'sidebar', 'body', 'footer'], [], true, '0.5.2', Scaffold::class),
            new WidgetMetadata('appBar', 'layout', 'Top bar content', ['title', 'actions'], [], true, '0.6.0', AppBar::class),
            new WidgetMetadata('sidebar', 'layout', 'Sidebar navigation', [], [], true, '0.6.0', Sidebar::class),
            new WidgetMetadata('mainContent', 'layout', 'Semantic main region', [], [], true, '0.6.0', MainContent::class),
            new WidgetMetadata('footer', 'layout', 'Footer content', [], [], true, '0.6.0', Footer::class),
            new WidgetMetadata('form', 'form', 'HTML form element', ['method', 'action', 'enctype'], [], true, '0.6.0', Form::class),
            new WidgetMetadata('label', 'form', 'Form label', ['for'], [], false, '0.6.0', Label::class),
            new WidgetMetadata('input', 'form', 'Text input', ['type', 'name', 'placeholder'], [], false, '0.6.0', Input::class),
            new WidgetMetadata('passwordInput', 'form', 'Password input', ['name', 'placeholder'], [], false, '0.6.0', PasswordInput::class),
            new WidgetMetadata('textarea', 'form', 'Multi-line input', ['rows', 'name'], [], false, '0.6.0', Textarea::class),
            new WidgetMetadata('checkbox', 'form', 'Checkbox', ['name', 'checked', 'label'], [], false, '0.6.0', Checkbox::class),
            new WidgetMetadata('radio', 'form', 'Radio button', ['name', 'value', 'checked', 'label'], [], false, '0.6.0', Radio::class),
            new WidgetMetadata('switch', 'form', 'Switch control (PHP: SwitchControl)', ['name', 'checked', 'label'], [], false, '0.6.0', SwitchControl::class),
            new WidgetMetadata('select', 'form', 'Select list', ['name', 'options', 'value'], [], false, '0.6.0', Select::class),
            new WidgetMetadata('fieldGroup', 'form', 'Label + control group', [], [], true, '0.6.0', FieldGroup::class),
            new WidgetMetadata('helperText', 'form', 'Helper copy', [], [], false, '0.6.0', HelperText::class),
            new WidgetMetadata('errorText', 'form', 'Error copy', [], [], false, '0.6.0', ErrorText::class),
            new WidgetMetadata('link', 'navigation', 'Anchor link', ['href', 'active', 'target'], [], false, '0.6.0', Link::class),
            new WidgetMetadata('table', 'data', 'HTML table', ['head', 'body', 'striped', 'hover', 'compact'], [], true, '0.7.0', Table::class),
            new WidgetMetadata('tableHead', 'data', 'Table head', [], [], true, '0.7.0', TableHead::class),
            new WidgetMetadata('tableBody', 'data', 'Table body', [], [], true, '0.7.0', TableBody::class),
            new WidgetMetadata('tableRow', 'data', 'Table row', [], [], true, '0.7.0', TableRow::class),
            new WidgetMetadata('tableCell', 'data', 'Table cell', [], [], true, '0.7.0', TableCell::class),
            new WidgetMetadata('tableHeaderCell', 'data', 'Table header cell', ['scope'], [], true, '0.7.0', TableHeaderCell::class),
            new WidgetMetadata('dataTable', 'data', 'Data table composer', ['headers', 'rows', 'child', 'striped', 'hover', 'compact'], [], true, '0.7.0', DataTable::class),
            new WidgetMetadata('searchField', 'form', 'Search field (render-only)', ['name', 'placeholder', 'value'], [], false, '0.7.0', SearchField::class),
            new WidgetMetadata('pagination', 'navigation', 'Pagination controls', ['currentPage', 'totalPages'], [], false, '0.7.0', Pagination::class),
            new WidgetMetadata('avatar', 'media', 'User avatar', ['image', 'initials', 'size', 'shape'], [], false, '0.7.0', Avatar::class),
            new WidgetMetadata('image', 'media', 'Image element', ['src', 'alt', 'loading'], [], false, '0.7.0', Image::class),
            new WidgetMetadata('logo', 'media', 'Brand logo', ['image', 'text', 'href'], [], false, '0.7.0', Logo::class),
            new WidgetMetadata('statCard', 'data', 'Statistic card', ['title', 'value', 'trend', 'color'], [], false, '0.7.0', StatCard::class),
            new WidgetMetadata('metric', 'data', 'Inline metric', ['label', 'value'], [], false, '0.7.0', Metric::class),
            new WidgetMetadata('emptyState', 'data', 'Empty state', ['title', 'description', 'action'], [], true, '0.7.0', EmptyState::class),
            new WidgetMetadata('activityList', 'data', 'Activity feed', [], [], true, '0.7.0', ActivityList::class),
            new WidgetMetadata('activityItem', 'data', 'Activity item', ['title', 'description', 'timestamp'], [], false, '0.7.0', ActivityItem::class),
            new WidgetMetadata('pageHeader', 'layout', 'Page header', ['title', 'subtitle', 'actions'], [], true, '0.7.0', PageHeader::class),
            new WidgetMetadata('toolbar', 'layout', 'Toolbar slots', ['leading', 'center', 'trailing'], [], true, '0.7.0', Toolbar::class),
            new WidgetMetadata('modal', 'overlay', 'Modal dialog', ['title', 'body', 'footer', 'open', 'ariaLabel'], ['close'], true, '0.9.0', Modal::class),
            new WidgetMetadata('drawer', 'overlay', 'Side drawer', ['side', 'title', 'body', 'open', 'ariaLabel'], ['close'], true, '0.9.0', Drawer::class),
            new WidgetMetadata('popover', 'overlay', 'Popover panel', ['trigger', 'content', 'open', 'ariaLabel'], [], true, '0.9.0', Popover::class),
            new WidgetMetadata('tooltip', 'overlay', 'Tooltip', ['text', 'child'], [], true, '0.9.0', Tooltip::class),
            new WidgetMetadata('toast', 'feedback', 'Toast live region', ['message', 'tone', 'open'], [], true, '0.9.0', Toast::class),
            new WidgetMetadata('alert', 'feedback', 'Alert banner', ['tone', 'title', 'body'], [], true, '0.9.0', Alert::class),
            new WidgetMetadata('badge', 'feedback', 'Badge', ['color'], [], false, '0.9.0', Badge::class),
            new WidgetMetadata('chip', 'feedback', 'Chip', ['color', 'dismissible'], [], false, '0.9.0', Chip::class),
            new WidgetMetadata('spinner', 'feedback', 'Spinner', ['size', 'label'], [], false, '0.9.0', Spinner::class),
            new WidgetMetadata('progress', 'feedback', 'Progress bar', ['value', 'max', 'label'], [], false, '0.9.0', Progress::class),
            new WidgetMetadata('skeleton', 'feedback', 'Skeleton placeholder', ['variant', 'lines'], [], false, '0.9.0', Skeleton::class),
            new WidgetMetadata('dropdown', 'navigation', 'Dropdown', ['trigger', 'items', 'open'], [], true, '0.9.0', Dropdown::class),
            new WidgetMetadata('dropdownMenu', 'navigation', 'Dropdown menu', [], [], true, '0.9.0', DropdownMenu::class),
            new WidgetMetadata('dropdownItem', 'navigation', 'Dropdown item', ['href', 'disabled'], [], false, '0.9.0', DropdownItem::class),
            new WidgetMetadata('accordion', 'layout', 'Accordion', ['item', 'multiple'], [], true, '0.9.0', Accordion::class),
            new WidgetMetadata('accordionItem', 'layout', 'Accordion item', ['title', 'content', 'open'], [], true, '0.9.0', AccordionItem::class),
            new WidgetMetadata('tabs', 'navigation', 'Tabs', ['tab'], [], true, '0.9.0', Tabs::class),
            new WidgetMetadata('tab', 'navigation', 'Tab definition', ['content', 'active'], [], true, '0.9.0', Tab::class),
            new WidgetMetadata('tabPanel', 'navigation', 'Tab panel', ['content'], [], true, '0.9.0', TabPanel::class),
        ];
    }
}
