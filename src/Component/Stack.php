<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\HasFill;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Layered layout context (Flutter Stack).
 *
 * v1 paints children in DOM order inside a relative container.
 * Future: absolute Positioned children without changing this public API.
 *
 * Does not claim width by default — call fill() for a full-bleed stack.
 */
final class Stack extends Component
{
    use HasChildren;
    use HasFill;
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
        $stack = new self();
        $stack->setChildren(self::normalizeChildren($children));

        return $stack;
    }

    /**
     * Clip overflowing layered children (additive class; default Stack unchanged).
     */
    public function clip(): self
    {
        return $this->class('overflow-hidden');
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('stack', [
            'fill' => $this->fillState(),
        ]));

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
