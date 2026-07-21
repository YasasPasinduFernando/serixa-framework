<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Primary page content. Renders semantic &lt;main&gt;.
 *
 * @example MainContent::make(Text::make('Welcome')->as('h1'))
 */
final class MainContent extends Component
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
        $main = new self();
        $main->setChildren(self::normalizeChildren($children));

        return $main;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('mainContent'));

        return sprintf('<main%s>%s</main>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
