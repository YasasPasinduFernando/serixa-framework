<?php

declare(strict_types=1);

namespace Serixa\Plugin;

use Serixa\Container\Container;
use Serixa\Exception\PluginException;
use Serixa\Registry\WidgetRegistry;

/**
 * Boots and tracks registered plugins (no marketplace â€” local composition only).
 */
final class PluginManager
{
    /** @var array<string, PluginInterface> */
    private array $plugins = [];

    public function __construct(
        private readonly Container $container = new Container(),
        private readonly WidgetRegistry $registry = new WidgetRegistry(),
    ) {
    }

    public function register(PluginInterface $plugin): void
    {
        $name = $plugin->name();

        if (isset($this->plugins[$name])) {
            throw new PluginException(sprintf('Plugin "%s" is already registered.', $name));
        }

        $plugin->boot($this->container, $this->registry);
        $this->plugins[$name] = $plugin;
    }

    public function has(string $name): bool
    {
        return isset($this->plugins[$name]);
    }

    /**
     * @return array<string, PluginInterface>
     */
    public function all(): array
    {
        return $this->plugins;
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function registry(): WidgetRegistry
    {
        return $this->registry;
    }
}
