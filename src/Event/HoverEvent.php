<?php

declare(strict_types=1);

namespace Serixa\Event;

use Serixa\Contracts\EventInterface;

/** Prepared hover event contract â€” no browser implementation yet. */
final class HoverEvent implements EventInterface
{
    public function name(): string
    {
        return 'hover';
    }
}
