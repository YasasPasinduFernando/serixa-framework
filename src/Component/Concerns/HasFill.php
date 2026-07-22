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
    private bool $fill = false;

    /**
     * Stretch to the parent's content box width (`w-full`).
     *
     * Prefer Expanded inside a Row when the child should claim remaining space.
     */
    public function fill(bool $fill = true): static
    {
        $this->fill = $fill;

        return $this;
    }

    protected function fillState(): bool
    {
        return $this->fill;
    }
}
