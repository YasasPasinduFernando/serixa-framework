<?php

declare(strict_types=1);

namespace Serixa\Tests\Support;

use PHPUnit\Framework\TestCase;
use Serixa\Support\ClassMerger;

final class ClassMergerCacheTest extends TestCase
{
    public function testMergeCacheReturnsIdenticalResults(): void
    {
        $merger = new ClassMerger();
        $a = $merger->merge(['flex', 'w-full'], ['w-56']);
        $b = $merger->merge(['flex', 'w-full'], ['w-56']);

        $this->assertSame($a, $b);
        $this->assertSame(['flex', 'w-56'], $a);
    }

    public function testConflictGroupCacheSurvivesAcrossMergers(): void
    {
        ClassMerger::clearConflictGroupCache();
        $m1 = new ClassMerger();
        $m2 = new ClassMerger();
        $this->assertSame(
            $m1->merge(['w-full'], ['w-10']),
            $m2->merge(['w-full'], ['w-10']),
        );
    }
}
