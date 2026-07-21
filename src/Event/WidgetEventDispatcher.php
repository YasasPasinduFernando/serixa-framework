<?php

declare(strict_types=1);

namespace Serixa\Event;

use Serixa\Contracts\EventInterface;
use Serixa\Contracts\WidgetInterface;

/**
 * Event dispatcher contract for future interactive widgets.
 *
 * Architecture only â€” does not attach DOM listeners in this milestone.
 */
interface WidgetEventDispatcher
{
    /**
     * @param callable(EventInterface): void $listener
     */
    public function listen(string $eventName, callable $listener): void;

    public function dispatch(EventInterface $event, ?WidgetInterface $widget = null): void;
}
