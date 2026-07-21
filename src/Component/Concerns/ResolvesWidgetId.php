<?php

declare(strict_types=1);

namespace Serixa\Component\Concerns;

/**
 * Stable DOM ids for ARIA relationships.
 *
 * @internal
 */
trait ResolvesWidgetId
{
    protected function resolveWidgetId(string $prefix): string
    {
        return $this->id ?? ($prefix . '-' . spl_object_id($this));
    }
}
