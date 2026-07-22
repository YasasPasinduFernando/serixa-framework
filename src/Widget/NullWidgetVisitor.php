<?php

declare(strict_types=1);

namespace Serixa\Widget;

use Serixa\Contracts\WidgetInterface;

/**
 * No-op widget visitor (stable hook for DevTools / plugins).
 */
final class NullWidgetVisitor implements WidgetVisitor
{
    public function visit(WidgetInterface $widget): void
    {
    }
}
