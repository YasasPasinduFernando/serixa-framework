<?php

declare(strict_types=1);

namespace Serixa\Performance;

/**
 * Contract for memory sampling during render.
 */
interface MemoryProfiler
{
    public function sample(string $label): int;

    public function peak(): int;
}
