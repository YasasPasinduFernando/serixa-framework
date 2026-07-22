<?php

declare(strict_types=1);

namespace Serixa\Widget;

use Serixa\Contracts\WidgetInterface;

/**
 * Visitor over live widgets (DevTools / plugins). See NullWidgetVisitor.
 */
interface WidgetVisitor
{
    public function visit(WidgetInterface $widget): void;
}
