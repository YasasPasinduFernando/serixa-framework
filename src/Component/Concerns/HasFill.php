<?php

declare(strict_types=1);

namespace Serixa\Component\Concerns;

/**
 * Opt-in horizontal fill (`w-full`) for generic layout widgets.
 *
 * Specialized layouts own sizing; generic Row/Column/Stack/Center stay
 * content-sized unless fill() is requested. Container defaults to filled
 * so max-width centering keeps working.
 */
trait HasFill
{
    protected bool $fill = false;

    /**
     * Stretch to the parent's content box width (`w-full`).
     *
     * Prefer Expanded inside a Row when the child should claim remaining space.
     */
    public function fill(bool $enabled = true): static
    {
        $this->fill = $enabled;

        return $this;
    }

    public function isFill(): bool
    {
        return $this->fill;
    }
}
