<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Dropdown menu panel container.
 *
 * @example DropdownMenu::make(DropdownItem::make('Edit'))
 */
final class DropdownMenu extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $menu = new self();
        $menu->setChildren(self::normalizeChildren($children));

        return $menu;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('dropdownMenu'),
            [
                'role' => 'menu',
                'data-serixa-dropdown-menu' => true,
            ],
        );

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
