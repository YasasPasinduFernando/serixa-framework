<?php

declare(strict_types=1);

namespace Serixa\Document\Asset;

/**
 * Resolves logical asset paths through an optional manifest and query versioning.
 */
final class AssetResolver
{
    public function __construct(
        private readonly ?AssetManifest $manifest = null,
        private readonly string $basePath = '/',
        private readonly ?string $versionQuery = null,
    ) {
    }

    public function resolve(string $path): string
    {
        $resolved = $this->manifest?->get($path) ?? $path;

        if (! str_starts_with($resolved, 'http://') && ! str_starts_with($resolved, 'https://') && ! str_starts_with($resolved, '//')) {
            $resolved = rtrim($this->basePath, '/') . '/' . ltrim($resolved, '/');
        }

        if ($this->versionQuery !== null && $this->versionQuery !== '') {
            $separator = str_contains($resolved, '?') ? '&' : '?';
            $resolved .= $separator . 'v=' . rawurlencode($this->versionQuery);
        }

        return $resolved;
    }
}
