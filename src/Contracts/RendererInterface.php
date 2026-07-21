<?php

declare(strict_types=1);

namespace Serixa\Contracts;

/**
 * Renders widgets (fragments) or documents (full HTML pages).
 */
interface RendererInterface
{
    /**
     * Render widgets, documents, or raw trusted HTML strings.
     *
     * @param DocumentInterface|WidgetInterface|string ...$nodes
     */
    public function render(DocumentInterface|WidgetInterface|string ...$nodes): string;
}
