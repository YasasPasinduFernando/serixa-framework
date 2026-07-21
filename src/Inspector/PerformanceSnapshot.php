<?php

declare(strict_types=1);

namespace Serixa\Inspector;

/**
 * Snapshot of timing / memory metrics for a render.
 */
final class PerformanceSnapshot
{
    public function __construct(
        private readonly string $renderId,
        private readonly float $durationMs = 0.0,
        private readonly int $memoryBytes = 0,
        private readonly int $passCount = 0,
    ) {
    }

    public function renderId(): string
    {
        return $this->renderId;
    }

    public function durationMs(): float
    {
        return $this->durationMs;
    }

    public function memoryBytes(): int
    {
        return $this->memoryBytes;
    }

    public function passCount(): int
    {
        return $this->passCount;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'renderId' => $this->renderId,
            'durationMs' => $this->durationMs,
            'memoryBytes' => $this->memoryBytes,
            'passCount' => $this->passCount,
        ];
    }
}
