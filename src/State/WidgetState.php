<?php

declare(strict_types=1);

namespace Serixa\State;

use Serixa\Contracts\StateInterface;

/**
 * Mutable in-memory state for a single widget instance key.
 */
final class WidgetState implements StateInterface
{
    /** @param array<string, mixed> $data */
    public function __construct(
        private array $data = [],
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function clear(): void
    {
        $this->data = [];
    }
}
