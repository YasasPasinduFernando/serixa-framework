<?php

declare(strict_types=1);

namespace Serixa\Pipeline\Pass;

use Serixa\Pipeline\RenderPassInterface;
use Serixa\Runtime\RenderContext;

/**
 * Placeholder pass for future tree-wide accessibility enrichment.
 *
 * Widgets already emit ARIA via AccessibilityBuilder; this pass is the extension point
 * for automated audits / injected attributes without changing Widget APIs.
 *
 * @internal
 */
final class AccessibilityPass implements RenderPassInterface
{
    public function name(): string
    {
        return 'accessibility';
    }

    public function handle(RenderContext $context): void
    {
        $context->setMeta('pipeline.accessibility', true);
    }
}
