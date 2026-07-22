<?php

declare(strict_types=1);

namespace Serixa\Performance;

use Serixa\Inspector\PerformanceSnapshot;

/**
 * Stable no-op profiler implementing the performance contracts.
 */
final class NullPerformanceProfiler implements PerformanceProfiler, MemoryProfiler, RenderProfiler
{
    /** @var array<string, float> */
    private array $started = [];

    /** @var array<string, float> */
    private array $passStarted = [];

    /** @var array<string, float> */
    private array $passDurations = [];

    private int $peak = 0;

    public function start(string $label): void
    {
        $this->started[$label] = microtime(true);
    }

    public function stop(string $label): void
    {
        unset($this->started[$label]);
    }

    public function snapshot(string $renderId): PerformanceSnapshot
    {
        $duration = 0.0;
        foreach ($this->passDurations as $ms) {
            $duration += $ms;
        }

        return new PerformanceSnapshot(
            renderId: $renderId,
            durationMs: $duration,
            memoryBytes: $this->peak(),
            passCount: count($this->passDurations),
        );
    }

    public function sample(string $label): int
    {
        $bytes = memory_get_usage(true);
        $this->peak = max($this->peak, $bytes);

        return $bytes;
    }

    public function peak(): int
    {
        $this->peak = max($this->peak, memory_get_peak_usage(true));

        return $this->peak;
    }

    public function beginPass(string $passName): void
    {
        $this->passStarted[$passName] = microtime(true);
    }

    public function endPass(string $passName): void
    {
        if (! isset($this->passStarted[$passName])) {
            return;
        }

        $this->passDurations[$passName] = round((microtime(true) - $this->passStarted[$passName]) * 1000, 3);
        unset($this->passStarted[$passName]);
    }

    public function passDurations(): array
    {
        return $this->passDurations;
    }
}
