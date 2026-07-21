<?php

declare(strict_types=1);

namespace Serixa\Document\Element;

/**
 * HTML &lt;meta&gt; element.
 */
final class Meta extends HeadElement
{
    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function __construct(
        private readonly array $attributes,
    ) {
        parent::__construct();
    }

    public static function charset(string $charset = 'UTF-8'): self
    {
        return new self(['charset' => $charset]);
    }

    public static function viewport(string $content = 'width=device-width, initial-scale=1'): self
    {
        return new self(['name' => 'viewport', 'content' => $content]);
    }

    public static function name(string $name, string $content): self
    {
        return new self(['name' => $name, 'content' => $content]);
    }

    public static function property(string $property, string $content): self
    {
        return new self(['property' => $property, 'content' => $content]);
    }

    public static function httpEquiv(string $httpEquiv, string $content): self
    {
        return new self(['http-equiv' => $httpEquiv, 'content' => $content]);
    }

    public function render(): string
    {
        return '<meta' . $this->attrs($this->attributes) . ' />';
    }

    /**
     * @return array<string, string|bool|null>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
}
