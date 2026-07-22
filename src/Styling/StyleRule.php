<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * One CSS rule: selector + properties.
 */
final class StyleRule
{
    /** @var list<StyleProperty> */
    private array $properties;

    /**
     * @param list<StyleProperty> $properties
     */
    public function __construct(
        private readonly StyleSelector $selector,
        array $properties = [],
    ) {
        $this->properties = array_values($properties);
    }

    public function selector(): StyleSelector
    {
        return $this->selector;
    }

    /**
     * @return list<StyleProperty>
     */
    public function properties(): array
    {
        return $this->properties;
    }

    public function withProperty(StyleProperty $property): self
    {
        $clone = clone $this;
        $clone->properties[] = $property;

        return $clone;
    }

    /**
     * Merge properties; later declarations for the same property win.
     *
     * @param list<StyleProperty> $properties
     */
    public function mergeProperties(array $properties): self
    {
        $byName = [];
        foreach ($this->properties as $property) {
            $byName[$property->name()] = $property;
        }
        foreach ($properties as $property) {
            $byName[$property->name()] = $property;
        }

        return new self($this->selector, array_values($byName));
    }

    public function toCss(): string
    {
        if ($this->properties === []) {
            return '';
        }

        $body = [];
        foreach ($this->properties as $property) {
            $body[] = $property->toCss();
        }

        return $this->selector->value() . '{' . implode(';', $body) . '}';
    }
}
