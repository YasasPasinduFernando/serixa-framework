<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic border-radius token. */
final class RadiusToken extends DesignToken
{
    public static function small(): self
    {
        return new self('radius.small');
    }

    public static function medium(): self
    {
        return new self('radius.medium');
    }

    public static function large(): self
    {
        return new self('radius.large');
    }

    public static function full(): self
    {
        return new self('radius.full');
    }
}
