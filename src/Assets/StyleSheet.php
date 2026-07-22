<?php

declare(strict_types=1);

namespace Serixa\Assets;

use Serixa\Document\Asset\AssetManager;
use Serixa\Styling\StyleSheet as StylingStyleSheet;

/**
 * @deprecated Use Serixa\Styling\StyleSheet — kept for backward compatibility.
 */
final class StyleSheet
{
    public const PUBLIC_URL = StylingStyleSheet::PUBLIC_URL;

    public const BASE_RELATIVE = StylingStyleSheet::FOUNDATION_RELATIVE;

    private function __construct()
    {
    }

    public static function basePath(): string
    {
        return StylingStyleSheet::foundationPath();
    }

    /**
     * @return array{path: string, bytes: int, sources: list<string>, ms: float, manifest?: \Serixa\Styling\ThemeManifest, rules?: int}
     */
    public static function build(string $projectRoot, string $outputRelative = 'public/assets/serixa.css'): array
    {
        return StylingStyleSheet::build($projectRoot, $outputRelative);
    }

    public static function register(AssetManager $assets, string $publicUrl = self::PUBLIC_URL): AssetManager
    {
        return StylingStyleSheet::register($assets, $publicUrl);
    }
}
