<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/** Table &lt;td&gt;. */
final class TableCell extends Component
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
        $cell = new self();
        $cell->setChildren(self::normalizeChildren($children));

        return $cell;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('tableCell'));

        return sprintf('<td%s>%s</td>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
