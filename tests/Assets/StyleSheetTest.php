<?php

declare(strict_types=1);

namespace Serixa\Tests\Assets;

use PHPUnit\Framework\TestCase;
use Serixa\Assets\StyleSheet;
use Serixa\Document\Asset\AssetManager;

final class StyleSheetTest extends TestCase
{
    public function testBasePathPointsAtShippedCss(): void
    {
        $this->assertFileExists(StyleSheet::basePath());
        $this->assertStringContainsString('serixa.css', StyleSheet::basePath());
    }

    public function testRegisterAddsPublicCssUrl(): void
    {
        $assets = new AssetManager();
        StyleSheet::register($assets);
        $collected = $assets->collect();

        $this->assertNotEmpty($collected);
        $this->assertSame('css', $collected[0]['type']);
        $this->assertSame('/assets/app.css', $collected[0]['path']);
    }

    public function testBuildMergesBaseIntoPublicAssets(): void
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'serixa-css-' . uniqid('', true);
        mkdir($root . '/resources/css', 0777, true);
        mkdir($root . '/public/assets', 0777, true);
        file_put_contents($root . '/resources/css/app.css', '/* app */\n.site-test{color:red}');

        $result = StyleSheet::build($root);

        $this->assertFileExists($root . '/public/assets/app.css');
        $this->assertFileExists($root . '/build/app.css');
        $this->assertGreaterThan(1000, $result['bytes']);
        $css = (string) file_get_contents($root . '/public/assets/app.css');
        $this->assertStringContainsString('.flex{', $css);
        $this->assertStringContainsString('site-test', $css);
        $this->assertContains('assets/css/serixa.css', $result['sources']);
        $this->assertContains('resources/css/app.css', $result['sources']);

        $this->deleteTree($root);
    }

    private function deleteTree(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteTree($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }
}
