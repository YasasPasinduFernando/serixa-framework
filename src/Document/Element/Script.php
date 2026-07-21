<?php

declare(strict_types=1);

namespace Serixa\Document\Element;

/**
 * HTML &lt;script&gt; element.
 */
final class Script extends HeadElement
{
    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function __construct(
        private readonly array $attributes = [],
        private readonly string $inline = '',
    ) {
        parent::__construct();
    }

    public static function src(string $src, bool $defer = false, bool $async = false): self
    {
        return new self([
            'src' => $src,
            'defer' => $defer ? true : null,
            'async' => $async ? true : null,
        ]);
    }

    /**
     * @param array<string, string|bool|null> $attributes
     */
    public static function inline(string $javascript, array $attributes = []): self
    {
        return new self($attributes, $javascript);
    }

    public static function jsonLd(string $json): self
    {
        return new self(['type' => 'application/ld+json'], $json);
    }

    public function render(): string
    {
        if ($this->inline !== '') {
            // Inline scripts / JSON-LD are trusted author content (not end-user HTML).
            return '<script' . $this->attrs($this->attributes) . '>' . $this->inline . '</script>';
        }

        return '<script' . $this->attrs($this->attributes) . '></script>';
    }

    /**
     * @return array<string, string|bool|null>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function inlineContent(): string
    {
        return $this->inline;
    }
}
