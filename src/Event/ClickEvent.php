<?php

declare(strict_types=1);

namespace Serixa\Event;

use Serixa\Contracts\EventInterface;

/** Prepared click event contract â€” no browser implementation yet. */
final class ClickEvent implements EventInterface
{
    public function name(): string
    {
        return 'click';
    }
}
