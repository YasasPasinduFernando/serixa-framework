<?php

declare(strict_types=1);

namespace Serixa\Performance;

/**
 * Contract for per-render / per-pass timing (implementation later).
 */
interface RenderProfiler
{
    public function beginPass(string $passName): void;

    public function endPass(string $passName): void;

    /**
     * @return array<string, float> pass name => duration ms
     */
    public function passDurations(): array;
}
