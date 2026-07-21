<?php

declare(strict_types=1);

namespace Serixa\Component\Concerns;

use Serixa\Exception\InvalidArgumentException;

/**
 * Progressive-enhancement DOM event metadata (data-serixa-on-*).
 *
 * Handlers are opaque names — no server execution. The client runtime may
 * look them up; without JS, markup remains valid HTML.
 *
 * @internal
 */
trait HasDomEvents
{
    /** @var array<string, string> */
    private array $domEvents = [];

    /**
     * @return static
     */
    public function onClick(string $handler): static
    {
        return $this->onDomEvent('click', $handler);
    }

    /**
     * @return static
     */
    public function onChange(string $handler): static
    {
        return $this->onDomEvent('change', $handler);
    }

    /**
     * @return static
     */
    public function onInput(string $handler): static
    {
        return $this->onDomEvent('input', $handler);
    }

    /**
     * @return static
     */
    public function onSubmit(string $handler): static
    {
        return $this->onDomEvent('submit', $handler);
    }

    /**
     * @return static
     */
    public function onFocus(string $handler): static
    {
        return $this->onDomEvent('focus', $handler);
    }

    /**
     * @return static
     */
    public function onBlur(string $handler): static
    {
        return $this->onDomEvent('blur', $handler);
    }

    /**
     * @return static
     */
    public function onKeyDown(string $handler): static
    {
        return $this->onDomEvent('keydown', $handler);
    }

    /**
     * @return static
     */
    public function onKeyUp(string $handler): static
    {
        return $this->onDomEvent('keyup', $handler);
    }

    /**
     * @return array<string, string>
     */
    protected function domEventAttributes(): array
    {
        $attrs = [];

        foreach ($this->domEvents as $event => $handler) {
            $attrs['data-serixa-on-' . $event] = $handler;
        }

        return $attrs;
    }

    /**
     * @return static
     */
    private function onDomEvent(string $event, string $handler): static
    {
        $handler = trim($handler);

        if ($handler === '') {
            throw new InvalidArgumentException('Event handler name must not be empty.');
        }

        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_.:-]*$/', $handler)) {
            throw new InvalidArgumentException(
                'Event handler name must be a safe identifier (letters, digits, _ . : -).',
            );
        }

        $this->domEvents[$event] = $handler;

        return $this;
    }
}
