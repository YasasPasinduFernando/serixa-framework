<?php

declare(strict_types=1);

namespace Serixa\Contracts;

/**
 * Marker for framework UI events (browser wiring comes in a later milestone).
 */
interface EventInterface
{
    public function name(): string;
}
