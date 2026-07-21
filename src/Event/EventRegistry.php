<?php

declare(strict_types=1);

namespace Serixa\Event;

use Serixa\Exception\InvalidArgumentException;

/**
 * Registers named progressive-enhancement handlers for widgets and plugins.
 *
 * Does not execute handlers. Maps opaque names (as emitted in data-serixa-on-*)
 * to metadata so future client modules and PHP plugins can discover bindings.
 */
final class EventRegistry
{
    /** @var array<string, array{event: string, description: string, widget: string|null}> */
    private array $handlers = [];

    public function register(string $name, string $event, string $description = '', ?string $widget = null): self
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Event handler name must not be empty.');
        }

        $this->handlers[$name] = [
            'event' => strtolower($event),
            'description' => $description,
            'widget' => $widget,
        ];

        return $this;
    }

    public function unregister(string $name): void
    {
        unset($this->handlers[$name]);
    }

    public function has(string $name): bool
    {
        return isset($this->handlers[$name]);
    }

    /**
     * @return array{event: string, description: string, widget: string|null}|null
     */
    public function get(string $name): ?array
    {
        return $this->handlers[$name] ?? null;
    }

    /**
     * @return array<string, array{event: string, description: string, widget: string|null}>
     */
    public function all(): array
    {
        return $this->handlers;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return array_keys($this->handlers);
    }

    /**
     * Handlers registered for a DOM event type (click, submit, …).
     *
     * @return array<string, array{event: string, description: string, widget: string|null}>
     */
    public function forEvent(string $event): array
    {
        $event = strtolower($event);
        $matched = [];

        foreach ($this->handlers as $name => $meta) {
            if ($meta['event'] === $event) {
                $matched[$name] = $meta;
            }
        }

        return $matched;
    }

    /**
     * JSON payload suitable for embedding or tooling (not required by the runtime).
     */
    public function toJson(): string
    {
        return json_encode($this->handlers, JSON_THROW_ON_ERROR);
    }
}
