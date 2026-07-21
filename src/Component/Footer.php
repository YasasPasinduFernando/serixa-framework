<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Footer content for Scaffold::footer() (Scaffold owns the &lt;footer&gt; landmark).
 *
 * @example Footer::make('© 2026 SERIXA')
 */
final class Footer extends Component
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
        $footer = new self();
        $footer->setChildren(self::normalizeChildren($children));

        return $footer;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('footer'));

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
