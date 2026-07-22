<?php

declare(strict_types=1);

namespace Serixa\Performance;

use Serixa\Inspector\PerformanceSnapshot;

/**
 * Contract for overall runtime performance profiling.
 */
interface PerformanceProfiler
{
    public function start(string $label): void;

    public function stop(string $label): void;

    public function snapshot(string $renderId): PerformanceSnapshot;
}
