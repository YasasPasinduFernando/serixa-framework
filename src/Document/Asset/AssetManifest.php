<?php

declare(strict_types=1);

namespace Serixa\Document\Asset;

/**
 * Versioned path map for resolved public assets (Vite-ready shape, no bundler).
 */
final class AssetManifest
{
    /**
     * @param array<string, string> $map logical path => public path
     */
    public function __construct(
        private array $map = [],
        private readonly string $version = '1',
    ) {
    }

    public function version(): string
    {
        return $this->version;
    }

    public function set(string $logical, string $public): self
    {
        $this->map[$logical] = $public;

        return $this;
    }

    public function get(string $logical): ?string
    {
        return $this->map[$logical] ?? null;
    }

    public function has(string $logical): bool
    {
        return isset($this->map[$logical]);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->map;
    }
}
