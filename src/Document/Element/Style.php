<?php

declare(strict_types=1);

namespace Serixa\Document\Element;

/**
 * HTML &lt;style&gt; element.
 */
final class Style extends HeadElement
{
    public function __construct(
        private readonly string $css,
        private readonly ?string $media = null,
    ) {
        parent::__construct();
    }

    public static function make(string $css, ?string $media = null): self
    {
        return new self($css, $media);
    }

    public function render(): string
    {
        $attrs = $this->media !== null ? $this->attrs(['media' => $this->media]) : '';

        // Author-provided CSS is trusted (not end-user HTML text).
        return '<style' . $attrs . '>' . $this->css . '</style>';
    }

    public function css(): string
    {
        return $this->css;
    }
}
