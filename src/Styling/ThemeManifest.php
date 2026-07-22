<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * Build metadata for a compiled theme stylesheet.
 */
final class ThemeManifest
{
    /**
     * @param list<string> $selectors
     * @param list<string> $tokens
     * @param list<string> $sources
     * @param array<string, int> $sizes
     */
    public function __construct(
        private readonly string $version,
        private readonly array $selectors,
        private readonly array $tokens,
        private readonly array $sources,
        private readonly array $sizes,
        private readonly string $builtAt,
        private readonly float $compileMs,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'built_at' => $this->builtAt,
            'compile_ms' => $this->compileMs,
            'css' => StyleSheet::PUBLIC_URL,
            'selectors' => $this->selectors,
            'selector_count' => count($this->selectors),
            'tokens' => $this->tokens,
            'sources' => $this->sources,
            'sizes' => $this->sizes,
        ];
    }

    public function toJson(): string
    {
        return (string) json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function write(string $absolutePath): void
    {
        $dir = dirname($absolutePath);
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new \RuntimeException(sprintf('Unable to create directory %s', $dir));
        }

        file_put_contents($absolutePath, $this->toJson());
    }
}
