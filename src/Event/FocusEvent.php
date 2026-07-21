<?php

declare(strict_types=1);

namespace Serixa\Event;

use Serixa\Contracts\EventInterface;

/** Prepared focus event contract â€” no browser implementation yet. */
final class FocusEvent implements EventInterface
{
    public function name(): string
    {
        return 'focus';
    }
}
