<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic z-index / stacking token. */
final class ZIndexToken extends DesignToken
{
    public static function base(): self
    {
        return new self('zIndex.base');
    }

    public static function dropdown(): self
    {
        return new self('zIndex.dropdown');
    }

    public static function sticky(): self
    {
        return new self('zIndex.sticky');
    }

    public static function modal(): self
    {
        return new self('zIndex.modal');
    }

    public static function toast(): self
    {
        return new self('zIndex.toast');
    }
}
