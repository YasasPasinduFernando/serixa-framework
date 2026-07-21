<?php

declare(strict_types=1);

namespace Serixa\Widget;

use Serixa\Contracts\WidgetInterface;

/**
 * Visitor over live widgets (preparation for DevTools / plugins).
 */
interface WidgetVisitor
{
    public function visit(WidgetInterface $widget): void;
}
