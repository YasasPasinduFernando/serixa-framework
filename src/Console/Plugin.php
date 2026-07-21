<?php

declare(strict_types=1);

namespace Serixa\Console;

/**
 * Facade for plugins to register CLI commands without touching the renderer.
 *
 * @example Plugin::registerCommand(new MyCommand())
 */
final class Plugin
{
    public static function registerCommand(CommandInterface $command): void
    {
        CommandRegistry::default()->register($command);
    }
}
