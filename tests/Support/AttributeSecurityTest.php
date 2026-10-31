<?php

declare(strict_types=1);

namespace Serixa\Tests\Support;

use PHPUnit\Framework\TestCase;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Support\AttributeBag;

final class AttributeSecurityTest extends TestCase
{
    public function testRejectsAttributeNameInjection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new AttributeBag())->build(['title onmouseover' => 'alert(1)']);
    }

    public function testRejectsObfuscatedScriptNavigation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new AttributeBag())->build(['href' => " \tjava\nscript:alert(1)"]);
    }

    public function testRejectsDataNavigation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new AttributeBag())->build(['href' => 'data:text/html,<script>alert(1)</script>']);
    }

    public function testKeepsSafeRelativeUrlsAndEscaping(): void
    {
        $this->assertSame(' href="/search?q=a&amp;b=c" aria-label="&quot;Search&quot;"', (new AttributeBag())->build([
            'href' => '/search?q=a&b=c',
            'aria-label' => '"Search"',
        ]));
    }
}
