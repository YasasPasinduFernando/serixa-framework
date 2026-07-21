<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Sidebar navigation content for Scaffold::sidebar() (Scaffold owns the &lt;aside&gt;).
 *
 * @example Sidebar::make()->child(Link::make('Home')->href('/'))
 */
final class Sidebar extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private string $ariaLabel = 'Sidebar';

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $sidebar = new self();
        $sidebar->setChildren(self::normalizeChildren($children));

        return $sidebar;
    }

    public function ariaLabel(string $label): self
    {
        $this->ariaLabel = $label;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('sidebar'),
            [
                'role' => 'navigation',
                'aria-label' => $this->ariaLabel,
            ],
        );

        return sprintf('<nav%s>%s</nav>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
