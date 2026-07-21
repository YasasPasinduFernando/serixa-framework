<?php

declare(strict_types=1);

namespace Serixa\Inspector;

use Serixa\Contracts\WidgetInterface;

/**
 * Contract for inspecting a single widget instance (DevTools â€” no UI).
 */
interface WidgetInspector
{
    public function inspect(WidgetInterface $widget): WidgetSnapshot;
}
