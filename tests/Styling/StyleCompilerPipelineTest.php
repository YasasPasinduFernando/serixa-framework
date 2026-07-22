<?php

declare(strict_types=1);

namespace Serixa\Tests\Styling;

use PHPUnit\Framework\TestCase;
use Serixa\Styling\StyleCompiler;
use Serixa\Styling\StyleSheet;
use Serixa\Styling\ThemeCompiler;

final class StyleCompilerPipelineTest extends TestCase
{
    private string $tmp = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'serixa-style-' . bin2hex(random_bytes(4));
        mkdir($this->tmp . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets', 0777, true);
        mkdir($this->tmp . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'css', 0777, true);
        file_put_contents(
            $this->tmp . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'app.css',
            ".app-only{color:red}\n",
        );
    }

    protected function tearDown(): void
    {
        $this->deleteTree($this->tmp);
        parent::tearDown();
    }

    public function testCompileWritesSerixaCssAliasAndManifest(): void
    {
        $result = (new StyleCompiler())->compile($this->tmp);

        $this->assertFileExists($result['path']);
        $this->assertGreaterThan(1000, $result['bytes']);
        $this->assertGreaterThan(0, $result['rules']);
        $this->assertContains('theme:compiler', $result['sources']);
        $this->assertSame('1.0.0', $result['manifest']->toArray()['version']);

        $css = (string) file_get_contents($result['path']);
        $this->assertStringContainsString('--sx-color-primary:', $css);
        $this->assertStringContainsString('.sx-btn-primary', $css);
        $this->assertStringContainsString('.app-only', $css);

        $alias = $this->tmp . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'app.css';
        $this->assertFileExists($alias);
        $this->assertSame($css, (string) file_get_contents($alias));
    }

    public function testStyleSheetBuildFacadeMatchesCompiler(): void
    {
        $a = StyleSheet::build($this->tmp);
        $b = (new StyleCompiler())->compile($this->tmp);
        $this->assertSame($a['bytes'], $b['bytes']);
        $this->assertCount(count((new ThemeCompiler())->tokenNames()), $a['manifest']->toArray()['tokens']);
    }

    public function testCompileIsDeterministicForSameInputs(): void
    {
        $first = (new StyleCompiler())->compile($this->tmp);
        $second = (new StyleCompiler())->compile($this->tmp);
        $this->assertSame(
            (string) file_get_contents($first['path']),
            (string) file_get_contents($second['path']),
        );
    }

    private function deleteTree(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }
}
