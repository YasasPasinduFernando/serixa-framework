<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic shadow token. */
final class ShadowToken extends DesignToken
{
    public static function small(): self
    {
        return new self('shadow.small');
    }

    public static function medium(): self
    {
        return new self('shadow.medium');
    }

    public static function large(): self
    {
        return new self('shadow.large');
    }
}
