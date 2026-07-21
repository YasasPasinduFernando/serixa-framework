<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic spacing token. */
final class SpacingToken extends DesignToken
{
    public static function small(): self
    {
        return new self('spacing.small');
    }

    public static function medium(): self
    {
        return new self('spacing.medium');
    }

    public static function large(): self
    {
        return new self('spacing.large');
    }
}
