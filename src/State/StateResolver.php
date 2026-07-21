<?php

declare(strict_types=1);

namespace Serixa\State;

use Serixa\Contracts\StateInterface;
use Serixa\Contracts\WidgetInterface;
use Serixa\Widget\Widget;

/**
 * Resolves the state bag for a widget (explicit key or object-id fallback).
 */
final class StateResolver
{
    public function __construct(
        private readonly StateManager $manager = new StateManager(),
    ) {
    }

    public function resolve(WidgetInterface $widget): StateInterface
    {
        $key = $widget instanceof Widget
            ? ($widget->getStateKey() ?? ('widget_' . spl_object_id($widget)))
            : ('widget_' . spl_object_id($widget));

        return $this->manager->get($key);
    }
}
