<?php

declare(strict_types=1);

namespace Serixa\Styling;

use Serixa\Document\Asset\AssetManager;

/**
 * Official SERIXA stylesheet registration and offline build entrypoint.
 *
 * Public URL is always /assets/serixa.css — never a CDN.
 */
final class StyleSheet
{
    public const PUBLIC_URL = '/assets/serixa.css';

    /** @deprecated Use PUBLIC_URL */
    public const LEGACY_PUBLIC_URL = '/assets/app.css';

    public const FOUNDATION_RELATIVE = 'assets/css/serixa.css';

    private function __construct()
    {
    }

    /**
     * Absolute path to the committed utility foundation stylesheet.
     */
    public static function foundationPath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::FOUNDATION_RELATIVE);
    }

    /**
     * @deprecated Use foundationPath()
     */
    public static function basePath(): string
    {
        return self::foundationPath();
    }

    /**
     * Compile theme + foundation + app CSS into public/assets/serixa.css.
     *
     * @return array{path: string, bytes: int, sources: list<string>, ms: float, manifest?: ThemeManifest, rules?: int}
     */
    public static function build(string $projectRoot, string $outputRelative = 'public/assets/serixa.css'): array
    {
        return (new StyleCompiler())->compile($projectRoot, $outputRelative);
    }

    /**
     * Register the compiled stylesheet on an asset manager.
     */
    public static function register(AssetManager $assets, string $publicUrl = self::PUBLIC_URL): AssetManager
    {
        return $assets->css($publicUrl);
    }

    /**
     * @return array<string, string> relative => absolute
     */
    public static function discoverAppSources(string $projectRoot): array
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
