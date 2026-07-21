<?php

declare(strict_types=1);

namespace Serixa\Registry;

/**
 * Descriptive metadata for a widget type (builders, DevTools, marketplace).
 */
final class WidgetMetadata
{
    /**
     * @param list<string> $supportedProperties
     * @param list<string> $supportedEvents
     */
    public function __construct(
        private readonly string $name,
        private readonly string $category = 'general',
        private readonly string $description = '',
        private readonly array $supportedProperties = [],
        private readonly array $supportedEvents = [],
        private readonly bool $acceptsChildren = false,
        private readonly string $versionIntroduced = '0.1.0',
        private readonly string $className = '',
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return list<string>
     */
    public function supportedProperties(): array
    {
        return $this->supportedProperties;
    }

    /**
     * @return list<string>
     */
    public function supportedEvents(): array
    {
        return $this->supportedEvents;
    }

    public function acceptsChildren(): bool
    {
        return $this->acceptsChildren;
    }

    public function versionIntroduced(): string
    {
        return $this->versionIntroduced;
    }

    public function className(): string
    {
        return $this->className;
    }
}
