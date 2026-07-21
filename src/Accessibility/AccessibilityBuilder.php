<?php

declare(strict_types=1);

namespace Serixa\Accessibility;

/**
 * Builds ARIA and related accessibility attributes for widgets.
 */
final class AccessibilityBuilder
{
    /** @var array<string, string|bool|null> */
    private array $attributes = [];

    public function role(string $role): self
    {
        $this->attributes['role'] = $role;

        return $this;
    }

    public function label(string $label): self
    {
        $this->attributes['aria-label'] = $label;

        return $this;
    }

    public function labelledBy(string $id): self
    {
        $this->attributes['aria-labelledby'] = $id;

        return $this;
    }

    public function describedBy(string $id): self
    {
        $this->attributes['aria-describedby'] = $id;

        return $this;
    }

    public function hidden(bool $hidden = true): self
    {
        $this->attributes['aria-hidden'] = $hidden ? 'true' : 'false';

        return $this;
    }

    public function expanded(bool $expanded): self
    {
        $this->attributes['aria-expanded'] = $expanded ? 'true' : 'false';

        return $this;
    }

    public function pressed(bool $pressed): self
    {
        $this->attributes['aria-pressed'] = $pressed ? 'true' : 'false';

        return $this;
    }

    public function live(string $politeness = 'polite'): self
    {
        $this->attributes['aria-live'] = $politeness;

        return $this;
    }

    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function merge(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    /**
     * @return array<string, string|bool|null>
     */
    public function toAttributes(): array
    {
        return $this->attributes;
    }
}
