<?php

declare(strict_types=1);

namespace Serixa\Inspector;

use Serixa\Tree\WidgetTree;

/**
 * Contract for inspecting a widget tree (DevTools â€” no UI).
 */
interface TreeInspector
{
    public function inspect(WidgetTree $tree): RenderSnapshot;
}
