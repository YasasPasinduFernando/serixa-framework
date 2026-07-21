<?php

declare(strict_types=1);

namespace Serixa\Widget;

use Countable;
use IteratorAggregate;
use Serixa\Contracts\WidgetInterface;
use Traversable;

/**
 * Ordered collection of widgets (composition helper, not a layout widget).
 *
 * @implements IteratorAggregate<int, WidgetInterface>
 */
final class WidgetCollection implements Countable, IteratorAggregate
{
    /** @var list<WidgetInterface> */
    private array $widgets = [];

    /**
     * @param list<WidgetInterface> $widgets
     */
    public function __construct(array $widgets = [])
    {
        foreach ($widgets as $widget) {
            $this->add($widget);
        }
    }

    public function add(WidgetInterface $widget): self
    {
        $this->widgets[] = $widget;

        return $this;
    }

    /**
     * @return list<WidgetInterface>
     */
    public function all(): array
    {
        return $this->widgets;
    }

    public function count(): int
    {
        return count($this->widgets);
    }

    public function isEmpty(): bool
    {
        return $this->widgets === [];
    }

    public function getIterator(): Traversable
    {
        yield from $this->widgets;
    }
}
