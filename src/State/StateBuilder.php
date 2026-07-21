<?php

declare(strict_types=1);

namespace Serixa\State;

/**
 * Fluent builder for initial widget state payloads.
 */
final class StateBuilder
{
    /** @var array<string, mixed> */
    private array $data = [];

    public static function make(): self
    {
        return new self();
    }

    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function merge(array $values): self
    {
        foreach ($values as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function toState(): WidgetState
    {
        return new WidgetState($this->data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
