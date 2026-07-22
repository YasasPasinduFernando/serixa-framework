<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic opacity token. */
final class OpacityToken extends DesignToken
{
    public static function disabled(): self
    {
        return new self('opacity.disabled');
    }

    public static function muted(): self
    {
        return new self('opacity.muted');
    }

    public static function overlay(): self
    {
        return new self('opacity.overlay');
    }
}
