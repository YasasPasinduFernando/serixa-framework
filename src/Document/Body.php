<?php

declare(strict_types=1);

namespace Serixa\Document;

use Serixa\Contracts\WidgetInterface;
use Serixa\Support\HtmlEscaper;

/**
 * Document &lt;body&gt; â€” holds widget children and optional attributes.
 */
final class Body
{
    /** @var list<WidgetInterface|string> */
    private array $children = [];

    /** @var array<string, string|bool|null> */
    private array $attributes = [];

    public function __construct(
        private readonly HtmlEscaper $escaper = new HtmlEscaper(),
    ) {
    }

    public static function make(WidgetInterface|string ...$children): self
    {
        $body = new self();
        $body->children = array_values($children);

        return $body;
    }

    public function child(WidgetInterface|string $child): self
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param list<WidgetInterface|string> $children
     */
    public function children(array $children): self
    {
        foreach ($children as $child) {
            $this->child($child);
        }

        return $this;
    }

    /**
     * @return list<WidgetInterface|string>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function class(string $class): self
    {
        $this->attributes['class'] = $class;

        return $this;
    }

    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function attributes(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    /**
     * @return array<string, string|bool|null>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Wrap pre-rendered inner HTML.
     */
    public function wrap(string $innerHtml): string
    {
        $attrString = '';

        foreach ($this->attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            $safeName = $this->escaper->escape($name);

            if ($value === true) {
                $attrString .= ' ' . $safeName;
                continue;
            }

            $attrString .= sprintf(' %s="%s"', $safeName, $this->escaper->escape((string) $value));
        }

        return '<body' . $attrString . '>' . $innerHtml . '</body>';
    }
}
