<?php

declare(strict_types=1);

namespace Serixa\Support;

use Serixa\Exception\InvalidArgumentException;

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

            if (preg_match('/^[a-zA-Z_:][a-zA-Z0-9_.:-]*$/D', $name) !== 1) {
                throw new InvalidArgumentException('Invalid HTML attribute name.');
            }

            $urlAttributes = ['href', 'src', 'action', 'formaction', 'xlink:href'];
            if (is_string($value) && in_array(strtolower($name), $urlAttributes, true)) {
                $normalized = preg_replace('/[\x00-\x20\x7f]+/', '', $value) ?? '';
                if (preg_match('/^(javascript|vbscript):/i', $normalized) === 1
                    || (strtolower($name) !== 'src' && preg_match('/^data:/i', $normalized) === 1)) {
                    throw new InvalidArgumentException('Executable URL schemes are not allowed.');
                }
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
