<?php

declare(strict_types=1);

namespace Serixa\Inspector;

/**
 * Snapshot of one widget for inspection / serialization.
 */
final class WidgetSnapshot
{
    /**
     * @param array<string, mixed> $attributes
     * @param list<string> $children
     */
    public function __construct(
        private readonly string $className,
        private readonly ?string $id = null,
        private readonly array $attributes = [],
        private readonly array $children = [],
    ) {
    }

    public function className(): string
    {
        return $this->className;
    }

    public function id(): ?string
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return list<string>
     */
    public function children(): array
    {
        return $this->children;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'className' => $this->className,
            'id' => $this->id,
            'attributes' => $this->attributes,
            'children' => $this->children,
        ];
    }
}
