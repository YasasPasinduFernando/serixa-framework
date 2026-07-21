<?php

declare(strict_types=1);

namespace Serixa\Inspector;

/**
 * Snapshot of a full render for inspection / DevTools.
 */
final class RenderSnapshot
{
    /**
     * @param list<WidgetSnapshot> $widgets
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private readonly string $renderId,
        private readonly array $widgets = [],
        private readonly array $metadata = [],
        private readonly int $nodeCount = 0,
    ) {
    }

    public function renderId(): string
    {
        return $this->renderId;
    }

    /**
     * @return list<WidgetSnapshot>
     */
    public function widgets(): array
    {
        return $this->widgets;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function nodeCount(): int
    {
        return $this->nodeCount;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'renderId' => $this->renderId,
            'nodeCount' => $this->nodeCount,
            'metadata' => $this->metadata,
            'widgets' => array_map(
                static fn (WidgetSnapshot $snapshot): array => $snapshot->toArray(),
                $this->widgets,
            ),
        ];
    }
}
