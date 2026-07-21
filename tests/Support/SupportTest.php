<?php

declare(strict_types=1);

namespace Serixa\Tests\Support;

use PHPUnit\Framework\TestCase;
use Serixa\Support\AttributeBag;
use Serixa\Support\ClassMerger;
use Serixa\Support\HtmlEscaper;

final class SupportTest extends TestCase
{
    public function testHtmlEscaperEncodesSpecialCharacters(): void
    {
        $escaper = new HtmlEscaper();

        $this->assertSame(
            '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;',
            $escaper->escape('<script>alert("x")</script>'),
        );
    }

    public function testAttributeBagOmitsNullAndFalse(): void
    {
        $builder = new AttributeBag();

        $this->assertSame(
            ' id="main" disabled class="btn"',
            $builder->build([
                'id' => 'main',
                'hidden' => false,
                'title' => null,
                'disabled' => true,
                'class' => 'btn',
            ]),
        );
    }

    public function testAttributeBagEscapesValues(): void
    {
        $builder = new AttributeBag();

        $this->assertSame(
            ' title="a &amp; b"',
            $builder->build(['title' => 'a & b']),
        );
    }

    public function testClassMergerRemovesDuplicatesAndPreservesOrder(): void
    {
        $merger = new ClassMerger();

        $this->assertSame(
            ['a', 'b', 'c'],
            $merger->merge(['a', 'b'], ['b', 'c'], ['']),
        );
        $this->assertSame('a b c', $merger->toString(['a', 'b'], ['b', 'c']));
    }
}
