<?php

declare(strict_types=1);

namespace Serixa\Console\Scaffold;

/**
 * Shared naming helpers for make:* generators.
 */
final class NameInflector
{
    public static function className(string $input, string $suffix = ''): string
    {
        $trimmed = preg_replace('/[^A-Za-z0-9_\\\\\/]/', '', $input) ?? $input;
        $trimmed = str_replace(['/', '\\'], '/', $trimmed);
        $parts = array_values(array_filter(explode('/', $trimmed), static fn (string $p): bool => $p !== ''));
        $last = $parts === [] ? 'Generated' : (string) array_pop($parts);
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $last)));

        if ($suffix !== '' && ! str_ends_with($studly, $suffix)) {
            $studly .= $suffix;
        }

        return $studly;
    }

    public static function relativeNamespace(string $input): string
    {
        $trimmed = str_replace(['/', '\\'], '/', $input);
        $parts = array_values(array_filter(explode('/', $trimmed), static fn (string $p): bool => $p !== ''));

        if (count($parts) <= 1) {
            return '';
        }

        array_pop($parts);

        return implode('\\', array_map(static fn (string $p): string => self::className($p), $parts));
    }
}
