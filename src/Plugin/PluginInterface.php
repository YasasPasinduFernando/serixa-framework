<?php

declare(strict_types=1);

namespace Serixa\Plugin;

use Serixa\Container\Container;
use Serixa\Registry\WidgetRegistry;

/**
 * Base plugin contract â€” boots into the runtime container and registry.
 */
interface PluginInterface
{
    public function name(): string;

    public function boot(Container $container, WidgetRegistry $registry): void;
}
