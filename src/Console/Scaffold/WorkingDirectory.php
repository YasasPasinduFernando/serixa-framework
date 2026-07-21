<?php

declare(strict_types=1);

namespace Serixa\Console\Scaffold;

/**
 * Tracks the active project working directory for make/dev/build commands.
 */
final class WorkingDirectory
{
    private static ?string $path = null;

    public static function set(string $path): void
    {
        self::$path = $path;
    }

    public static function get(): string
    {
        return self::$path ?? (getcwd() ?: '.');
    }
}
