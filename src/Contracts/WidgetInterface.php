<?php

declare(strict_types=1);

namespace Serixa\Contracts;

/**
 * A renderable UI widget with optional lifecycle and tree participation.
 */
interface WidgetInterface
{
    /**
     * Render this widget to an HTML string.
     */
    public function render(): string;

    /**
     * Called once before the widget enters the active tree.
     */
    public function beforeMount(): void;

    /**
     * Called once after the widget has entered the active tree.
     */
    public function mounted(): void;

    /**
     * Called immediately before HTML is produced for this widget.
     */
    public function beforeRender(): void;

    /**
     * Called immediately after HTML has been produced for this widget.
     */
    public function afterRender(): void;

    /**
     * Called when the widget is removed from the tree / request scope.
     */
    public function destroy(): void;

    /**
     * Child nodes for WidgetTree construction (widgets or escaped text strings).
     *
     * @return list<WidgetInterface|string>
     */
    public function childrenForTree(): array;
}
