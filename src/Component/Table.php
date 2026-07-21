<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Semantic HTML table root.
 *
 * @example Table::make()->head(TableRow::make(...))->body(TableRow::make(...))
 */
final class Table extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private bool $striped = false;

    private bool $hover = false;

    private bool $compact = false;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $table = new self();
        $table->setChildren(self::normalizeChildren($children));

        return $table;
    }

    /**
     * Append a {@see TableHead}, or wrap the given children in one.
     *
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public function head(WidgetInterface|string|array ...$children): self
    {
        if (count($children) === 1 && $children[0] instanceof TableHead) {
            return $this->child($children[0]);
        }

        return $this->child(TableHead::make(...$children));
    }

    /**
     * Append a {@see TableBody}, or wrap the given children in one.
     *
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public function body(WidgetInterface|string|array ...$children): self
    {
        if (count($children) === 1 && $children[0] instanceof TableBody) {
            return $this->child($children[0]);
        }

        return $this->child(TableBody::make(...$children));
    }

    public function striped(bool $striped = true): self
    {
        $this->striped = $striped;

        return $this;
    }

    public function hover(bool $hover = true): self
    {
        $this->hover = $hover;

        return $this;
    }

    public function compact(bool $compact = true): self
    {
        $this->compact = $compact;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('table', [
            'striped' => $this->striped,
            'hover' => $this->hover,
            'compact' => $this->compact,
        ]));

        return sprintf('<table%s>%s</table>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
