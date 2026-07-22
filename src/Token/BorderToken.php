<?php

declare(strict_types=1);

namespace Serixa\Token;

/** Semantic border token. */
final class BorderToken extends DesignToken
{
    public static function subtle(): self
    {
        return new self('border.subtle');
    }

    public static function strong(): self
    {
        return new self('border.strong');
    }

    public static function focus(): self
    {
        return new self('border.focus');
    }
}
