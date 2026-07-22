<?php

declare(strict_types=1);

namespace Serixa\Document\Asset;

use Serixa\Document\Element\Link;
use Serixa\Document\Element\Script;
use Serixa\Document\Head;

/**
 * Registers CSS/JS/fonts/icons and emits head elements (architecture only â€” no Vite).
 */
final class AssetManager
{
    /** @var list<AssetBundle> */
    private array $bundles = [];

    /** @var list<array{type: string, path: string, options: array<string, mixed>}> */
    private array $loose = [];

    /** @var list<string> */
    private array $inlineScripts = [];

    public function __construct(
        private AssetResolver $resolver = new AssetResolver(),
    ) {
    }

    public function resolver(): AssetResolver
    {
        return $this->resolver;
    }

    public function setResolver(AssetResolver $resolver): self
    {
        $this->resolver = $resolver;

        return $this;
    }

    public function bundle(AssetBundle $bundle): self
    {
        $this->bundles[] = $bundle;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function css(string $path, array $options = []): self
    {
        $this->loose[] = ['type' => 'css', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function js(string $path, array $options = []): self
    {
        $this->loose[] = ['type' => 'js', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * Register trusted inline JavaScript emitted before the closing &lt;/head&gt;.
     */
    public function inlineScript(string $javascript): self
    {
        $trimmed = trim($javascript);
        if ($trimmed !== '') {
            $this->inlineScripts[] = $trimmed;
        }

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function font(string $path, array $options = []): self
    {
        $this->loose[] = ['type' => 'font', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function icon(string $path, array $options = []): self
    {
        $this->loose[] = ['type' => 'icon', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function preload(string $path, string $as, array $options = []): self
    {
        $this->loose[] = [
            'type' => 'preload',
            'path' => $path,
            'options' => array_merge(['as' => $as], $options),
        ];

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function prefetch(string $path, array $options = []): self
    {
        $this->loose[] = ['type' => 'prefetch', 'path' => $path, 'options' => $options];

        return $this;
    }

    /**
     * @return list<AssetBundle>
     */
    public function bundles(): array
    {
        return $this->bundles;
    }

    /**
     * Apply registered assets onto a Head instance.
     */
    public function applyTo(Head $head): void
    {
        foreach ($this->collect() as $asset) {
            $url = $this->resolver->resolve($asset['path']);
            $options = $asset['options'];

            match ($asset['type']) {
                'css' => $head->link(Link::stylesheet($url, $this->stringOptions($options))),
                'js' => $head->script(Script::src(
                    $url,
                    defer: (bool) ($options['defer'] ?? true),
                    async: (bool) ($options['async'] ?? false),
                )),
                'font' => $head->link(Link::preload($url, 'font', array_merge(
                    ['type' => (string) ($options['type'] ?? 'font/woff2'), 'crossorigin' => true],
                    $this->stringOptions($options),
                ))),
                'icon' => $head->link(Link::icon($url, (string) ($options['type'] ?? 'image/x-icon'))),
                'preload' => $head->link(Link::preload(
                    $url,
                    (string) ($options['as'] ?? 'fetch'),
                    $this->stringOptions($options),
                )),
                'prefetch' => $head->link(Link::prefetch($url, $this->stringOptions($options))),
                'image' => $head->link(Link::preload($url, 'image', $this->stringOptions($options))),
                default => null,
            };
        }

        foreach ($this->inlineScripts as $javascript) {
            $head->script(Script::inline($javascript));
        }
    }

    /**
     * @return list<array{type: string, path: string, options: array<string, mixed>}>
     */
    public function collect(): array
    {
        $assets = $this->loose;

        foreach ($this->bundles as $bundle) {
            foreach ($bundle->all() as $asset) {
                $assets[] = $asset;
            }
        }

        return $assets;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, string|bool|null>
     */
    private function stringOptions(array $options): array
    {
        $result = [];

        foreach ($options as $key => $value) {
            if (in_array($key, ['defer', 'async', 'as', 'type'], true)) {
                continue;
            }

            if (is_bool($value) || is_string($value) || $value === null) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
