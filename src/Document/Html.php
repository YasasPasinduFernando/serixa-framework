<?php

declare(strict_types=1);

namespace Serixa\Document;

use Serixa\Support\HtmlEscaper;

/**
 * Root &lt;html&gt; element wrapper.
 */
final class Html
{
    public function __construct(
        private readonly string $language = 'en',
        private readonly string $dir = 'ltr',
        private readonly HtmlEscaper $escaper = new HtmlEscaper(),
    ) {
    }

    public static function make(string $language = 'en', string $dir = 'ltr'): self
    {
        return new self($language, $dir);
    }

    public function language(): string
    {
        return $this->language;
    }

    public function dir(): string
    {
        return $this->dir;
    }

    public function wrap(string $innerHtml): string
    {
        return sprintf(
            '<!DOCTYPE html><html lang="%s" dir="%s">%s</html>',
            $this->escaper->escape($this->language),
            $this->escaper->escape($this->dir),
            $innerHtml,
        );
    }
}
