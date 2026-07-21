<?php

declare(strict_types=1);

namespace Serixa\State;

use Serixa\Contracts\StateInterface;

/**
 * Process-scoped, memory-only state registry.
 *
 * No HTTP, sessions, cookies, or database â€” prepared for Modal/Tabs/etc.
 * Injectable later via constructor; static current() mirrors ThemeManager convention.
 */
final class StateManager
{
    private static ?self $instance = null;

    /** @var array<string, WidgetState> */
    private array $states = [];

    public static function current(): self
    {
        return self::$instance ??= new self();
    }

    public static function set(self $manager): void
    {
        self::$instance = $manager;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public function get(string $key): WidgetState
    {
        return $this->states[$key] ??= new WidgetState();
    }

    public function put(string $key, StateInterface $state): void
    {
        $this->states[$key] = $state instanceof WidgetState
            ? $state
            : new WidgetState($state->all());
    }

    public function has(string $key): bool
    {
        return isset($this->states[$key]);
    }

    public function forget(string $key): void
    {
        unset($this->states[$key]);
    }

    public function clear(): void
    {
        $this->states = [];
    }
}
