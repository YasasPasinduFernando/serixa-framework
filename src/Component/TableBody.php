<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/** Table &lt;tbody&gt;. */
final class TableBody extends Component
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
        $body = new self();
        $body->setChildren(self::normalizeChildren($children));

        return $body;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('tableBody'));

        return sprintf('<tbody%s>%s</tbody>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
