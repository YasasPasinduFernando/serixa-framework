<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * Serializes style rules and CSS strings to disk.
 */
final class CssWriter
{
    /**
     * @param list<StyleRule> $rules
     */
    public function stringify(array $rules, string $banner = ''): string
    {
        $chunks = [];
        if ($banner !== '') {
            $chunks[] = '/* ' . str_replace(['*/', "\n"], ['', ' '], $banner) . ' */';
        }

        foreach ($rules as $rule) {
            $css = $rule->toCss();
            if ($css !== '') {
                $chunks[] = $css;
            }
        }

        return implode('', $chunks);
    }

    public function write(string $absolutePath, string $css): int
    {
        $dir = dirname($absolutePath);
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new \RuntimeException(sprintf('Unable to create directory %s', $dir));
        }

        $bytes = file_put_contents($absolutePath, $css);
        if ($bytes === false) {
            throw new \RuntimeException(sprintf('Unable to write %s', $absolutePath));
        }

        return $bytes;
    }
}
