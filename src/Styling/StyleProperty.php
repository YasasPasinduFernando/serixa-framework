<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * A single CSS declaration (property: value).
 */
final class StyleProperty
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name && $this->value === $other->value;
    }

    public function toCss(): string
    {
        return $this->name . ':' . $this->value;
    }
}
