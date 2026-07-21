<?php

declare(strict_types=1);

namespace Serixa\Token;

/**
 * Named design token. Themes map tokens to CSS classes â€” widgets never hard-code utilities.
 */
abstract class DesignToken
{
    public function __construct(
        private readonly string $name,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
