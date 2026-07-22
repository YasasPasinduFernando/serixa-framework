<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic breakpoint token (layout / responsive seams). */
final class BreakpointToken extends DesignToken
{
    public static function sm(): self
    {
        return new self('breakpoint.sm');
    }

    public static function md(): self
    {
        return new self('breakpoint.md');
    }

    public static function lg(): self
    {
        return new self('breakpoint.lg');
    }

    public static function xl(): self
    {
        return new self('breakpoint.xl');
    }
}
