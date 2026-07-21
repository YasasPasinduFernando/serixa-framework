<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic color token. */
final class ColorToken extends DesignToken
{
    public static function primary(): self
    {
        return new self('color.primary');
    }

    public static function secondary(): self
    {
        return new self('color.secondary');
    }

    public static function danger(): self
    {
        return new self('color.danger');
    }

    public static function surface(): self
    {
        return new self('color.surface');
    }

    public static function background(): self
    {
        return new self('color.background');
    }

    public static function textPrimary(): self
    {
        return new self('color.textPrimary');
    }

    public static function textMuted(): self
    {
        return new self('color.textMuted');
    }
}
