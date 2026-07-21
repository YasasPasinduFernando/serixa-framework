<?php

declare(strict_types=1);

namespace Serixa\Component\Concerns;

use Serixa\Contracts\ThemeInterface;
use Serixa\Theme\ThemeManager;

/**
 * Resolves the active theme for class lookup.
 *
 * @internal
 */
trait ResolvesTheme
{
    protected function theme(): ThemeInterface
    {
        return ThemeManager::current();
    }
}
