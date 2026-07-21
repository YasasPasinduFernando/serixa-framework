<?php

declare(strict_types=1);

namespace Serixa\Document\Element;

/**
 * HTML &lt;title&gt; element.
 */
final class Title extends HeadElement
{
    public function __construct(
        private readonly string $content,
    ) {
        parent::__construct();
    }

    public static function make(string $content): self
    {
        return new self($content);
    }

    public function render(): string
    {
        return '<title>' . $this->e($this->content) . '</title>';
    }

    public function content(): string
    {
        return $this->content;
    }
}
