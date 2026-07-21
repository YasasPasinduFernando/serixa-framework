<?php

declare(strict_types=1);

namespace Serixa\Pipeline\Pass;

use Serixa\Pipeline\RenderPassInterface;
use Serixa\Runtime\RenderContext;
use Serixa\Theme\ThemeManager;

/**
 * Bridges WidgetContext theme into ThemeManager for BC with existing widgets.
 *
 * @internal
 */
final class ThemeResolutionPass implements RenderPassInterface
{
    public function name(): string
    {
        return 'theme_resolution';
    }

    public function handle(RenderContext $context): void
    {
        ThemeManager::set($context->widgetContext()->theme());
        $context->setMeta('pipeline.theme_resolved', true);
    }
}
