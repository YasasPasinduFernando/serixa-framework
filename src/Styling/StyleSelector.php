<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * CSS selector for a compiled style rule.
 */
final class StyleSelector
{
    public function __construct(
        private readonly string $selector,
    ) {
    }

    public static function className(string $class): self
    {
        $class = ltrim(trim($class), '.');

        return new self('.' . $class);
    }

    public static function raw(string $selector): self
    {
        return new self(trim($selector));
    }

    public function value(): string
    {
        return $this->selector;
    }

    public function equals(self $other): bool
    {
        return $this->selector === $other->selector;
    }
}
