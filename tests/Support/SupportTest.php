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

    public function testClassMergerResolvesWidthConflictsLastWins(): void
    {
        $merger = new ClassMerger();

        $this->assertSame(
            ['flex', 'flex-col', 'w-56'],
            $merger->merge(['flex', 'flex-col', 'w-full'], ['w-56']),
        );
        $this->assertSame(
            ['mx-auto', 'w-auto', 'max-w-full'],
            $merger->merge(['mx-auto', 'w-full', 'max-w-screen-xl'], ['max-w-full'], ['w-auto']),
        );
        $this->assertSame(
            ['flex-none'],
            $merger->merge(['flex-1'], ['flex-none']),
        );
        $this->assertSame(
            ['min-w-full'],
            $merger->merge(['min-w-0'], ['min-w-full']),
        );
        $this->assertSame(
            ['p-2', 'sm:p-8'],
            $merger->merge(['p-4', 'sm:p-6'], ['p-2', 'sm:p-8']),
        );
        $this->assertSame(
            ['bg-white', 'dark:bg-slate-900'],
            $merger->merge(['bg-slate-50', 'dark:bg-slate-800'], ['bg-white', 'dark:bg-slate-900']),
        );
    }
}
