<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic typography token. */
final class TypographyToken extends DesignToken
{
    public static function body(): self
    {
        return new self('typography.body');
    }

    public static function heading(): self
    {
        return new self('typography.heading');
    }

    public static function label(): self
    {
        return new self('typography.label');
    }

    public static function muted(): self
    {
        return new self('typography.muted');
    }
}
