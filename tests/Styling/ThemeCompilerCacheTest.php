<?php

declare(strict_types=1);

namespace Serixa\Tests\Styling;

use PHPUnit\Framework\TestCase;
use Serixa\Styling\ThemeCompiler;

final class ThemeCompilerCacheTest extends TestCase
{
    public function testCompileIsMemoizedUntilForget(): void
    {
        $compiler = new ThemeCompiler();
        $a = $compiler->compile();
        $b = $compiler->compile();
        $this->assertSame($a, $b);

        $compiler->forget();
        $c = $compiler->compile();
        $this->assertNotSame($a, $c);
        $this->assertCount(count($a), $c);
    }
}
