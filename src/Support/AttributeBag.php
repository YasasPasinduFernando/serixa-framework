<?php

declare(strict_types=1);

namespace Serixa\Support;

/**
 * Builds a space-prefixed HTML attribute string from a name/value map.
 *
 * Boolean true emits a minimized attribute (e.g. disabled).
 * Null or false omits the attribute.
 */
final class AttributeBag
{
    public function __construct(
        private readonly HtmlEscaper $escaper = new HtmlEscaper(),
    ) {
    }

    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function build(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            $safeName = $this->escaper->escape($name);

            if ($value === true) {
                $parts[] = $safeName;
                continue;
            }

            $parts[] = sprintf('%s="%s"', $safeName, $this->escaper->escape($value));
        }

        if ($parts === []) {
            return '';
        }

        return ' ' . implode(' ', $parts);
    }
}
