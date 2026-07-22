<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic transition / motion duration token. */
final class TransitionToken extends DesignToken
{
    public static function fast(): self
    {
        return new self('transition.fast');
    }

    public static function normal(): self
    {
        return new self('transition.normal');
    }

    public static function slow(): self
    {
        return new self('transition.slow');
    }
}
