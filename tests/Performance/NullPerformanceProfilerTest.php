<?php

declare(strict_types=1);

namespace Serixa\Tests\Performance;

use PHPUnit\Framework\TestCase;
use Serixa\Performance\NullPerformanceProfiler;

final class NullPerformanceProfilerTest extends TestCase
{
    public function testProfilerRecordsPassesAndPeak(): void
    {
        $p = new NullPerformanceProfiler();
        $p->beginPass('render');
        $p->sample('mid');
        usleep(1000);
        $p->endPass('render');

        $this->assertArrayHasKey('render', $p->passDurations());
        $this->assertGreaterThan(0, $p->peak());

        $snap = $p->snapshot('r1');
        $this->assertSame('r1', $snap->renderId());
        $this->assertSame(1, $snap->passCount());
    }
}
