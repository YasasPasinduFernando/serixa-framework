<?php

declare(strict_types=1);

namespace Serixa\Document\Asset;

/**
 * A named collection of related assets (e.g. "app", "vendor").
 */
final class AssetBundle
{
    /** @var list<array{type: string, path: string, options: array<string, mixed>}> */
    private array $assets = [];

    public function __construct(
        private readonly string $name,
        private readonly string $version = '1.0.0',
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): string
    {
        return $this->version;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function css(string $path, array $options = []): self
    {
        $this->assets[] = ['type' => 'css', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function js(string $path, array $options = []): self
    {
        $this->assets[] = ['type' => 'js', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function font(string $path, array $options = []): self
    {
        $this->assets[] = ['type' => 'font', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function image(string $path, array $options = []): self
    {
        $this->assets[] = ['type' => 'image', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function icon(string $path, array $options = []): self
    {
        $this->assets[] = ['type' => 'icon', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @return list<array{type: string, path: string, options: array<string, mixed>}>
     */
    public function all(): array
    {
        return $this->assets;
    }
}
