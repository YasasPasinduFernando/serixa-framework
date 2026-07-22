<?php

declare(strict_types=1);

namespace Serixa\Assets;

/**
 * Official SERIXA stylesheet paths and offline CSS build helpers.
 *
 * Base utilities ship in framework/assets/css/serixa.css (compiled from
 * DefaultTheme + package themes). Apps append resources/css/app.css overrides.
 * Public URL is always /assets/app.css — never a CDN.
 */
final class StyleSheet
{
    public const PUBLIC_URL = '/assets/app.css';

    public const BASE_RELATIVE = 'assets/css/serixa.css';

    private function __construct()
    {
    }

    /**
     * Absolute path to the shipped SERIXA base stylesheet.
     */
    public static function basePath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::BASE_RELATIVE);
    }

    /**
     * Merge base + app CSS into the project's public asset path (offline).
     *
     * @return array{path: string, bytes: int, sources: list<string>, ms: float}
     */
    public static function build(string $projectRoot, string $outputRelative = 'public/assets/app.css'): array
    {
        $started = microtime(true);
        $projectRoot = rtrim($projectRoot, '/\\');
        $output = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outputRelative);
        $outputDir = dirname($output);

        if (! is_dir($outputDir) && ! mkdir($outputDir, 0775, true) && ! is_dir($outputDir)) {
            throw new \RuntimeException(sprintf('Unable to create directory %s', $outputDir));
        }

        $sources = [];
        $chunks = [];

        $base = self::basePath();
        if (! is_file($base)) {
            throw new \RuntimeException(sprintf(
                'SERIXA base stylesheet missing at %s. Reinstall serixa/framework.',
                $base,
            ));
        }
        $chunks[] = (string) file_get_contents($base);
        $sources[] = self::BASE_RELATIVE;

        foreach (self::appSources($projectRoot) as $relative => $absolute) {
            $chunks[] = "\n/* --- {$relative} --- */\n" . (string) file_get_contents($absolute);
            $sources[] = $relative;
        }

        $css = implode("\n", $chunks);
        if (file_put_contents($output, $css) === false) {
            throw new \RuntimeException(sprintf('Unable to write %s', $output));
        }

        $buildCopy = $projectRoot . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'app.css';
        $buildDir = dirname($buildCopy);
        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0775, true);
        }
        file_put_contents($buildCopy, $css);

        return [
            'path' => $output,
            'bytes' => strlen($css),
            'sources' => $sources,
            'ms' => round((microtime(true) - $started) * 1000, 2),
        ];
    }

    /**
     * Register the compiled stylesheet on an asset manager.
     */
    public static function register(\Serixa\Document\Asset\AssetManager $assets, string $publicUrl = self::PUBLIC_URL): \Serixa\Document\Asset\AssetManager
    {
        return $assets->css($publicUrl);
    }

    /**
     * @return array<string, string> relative => absolute
     */
    private static function appSources(string $projectRoot): array
    {
        $candidates = [
            'resources/css/app.css',
            'resources/app.css',
            'assets/demo.css',
            'public/app.css',
        ];

        foreach ($candidates as $relative) {
            $absolute = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (is_file($absolute)) {
                return [$relative => $absolute];
            }
        }

        return [];
    }
}
