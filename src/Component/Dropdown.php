<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Trigger + menu dropdown (SSR + DropdownModule keyboard nav).
 *
 * @example Dropdown::make()->trigger(Button::make('Menu'))->items([DropdownItem::make('Profile')])
 */
final class Dropdown extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    private WidgetInterface|string|null $trigger = null;

    /** @var list<WidgetInterface|string> */
    private array $items = [];

    private ?DropdownMenu $menu = null;

    private bool $open = false;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function trigger(WidgetInterface|string $trigger): self
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * @param list<WidgetInterface|string> $items
     */
    public function items(array $items): self
    {
        $this->items = array_values($items);

        return $this;
    }

    public function menu(DropdownMenu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function open(bool $open = true): self
    {
        $this->open = $open;

        return $this;
    }

    public function childrenForTree(): array
    {
        $nodes = [];

        if ($this->trigger !== null) {
            $nodes[] = $this->trigger;
        }

        if ($this->menu !== null) {
            $nodes[] = $this->menu;
        }

        return array_merge($nodes, $this->items);
    }

    public function render(): string
    {
        if ($this->trigger === null) {
            throw new InvalidArgumentException('Dropdown requires trigger().');
        }

        $id = $this->resolveWidgetId('dropdown');
        $menuId = $id . '-menu';

        $menuWidget = $this->menu ?? DropdownMenu::make($this->items);

        $triggerHtml = sprintf(
            '<div%s data-serixa-dropdown-trigger aria-haspopup="menu" aria-expanded="%s" aria-controls="%s">%s</div>',
            $this->buildStructuralAttributeString($this->theme()->classes('dropdown.trigger')),
            $this->open ? 'true' : 'false',
            $this->escape($menuId),
            $this->renderChild($this->trigger),
        );

        // Panel owns role=menu; render item children to avoid nested menus.
        $menuHtml = sprintf(
            '<div%s id="%s" role="menu" data-serixa-dropdown-panel%s>%s</div>',
            $this->buildStructuralAttributeString($this->theme()->classes('dropdown.panel', ['open' => $this->open])),
            $this->escape($menuId),
            $this->open ? '' : ' hidden',
            $this->renderChildren($menuWidget->childrenForTree()),
        );

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('dropdown'),
            [
                'id' => $id,
                'data-serixa-dropdown' => true,
                'data-serixa-open' => $this->open ? 'true' : 'false',
            ],
        );

        return sprintf('<div%s>%s%s</div>', $rootAttrs, $triggerHtml, $menuHtml);
    }
}
