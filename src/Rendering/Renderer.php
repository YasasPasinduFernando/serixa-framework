<?php

declare(strict_types=1);

namespace Serixa\Rendering;

use Serixa\Contracts\DocumentInterface;
use Serixa\Contracts\RendererInterface;
use Serixa\Contracts\WidgetInterface;
use Serixa\Document\DocumentRenderer;
use Serixa\Pipeline\RenderPipeline;
use Serixa\Runtime\RenderContext;
use Serixa\Runtime\WidgetContext;
use Serixa\Tree\WidgetTree;

/**
 * Render coordinator: fragments via pipeline, documents via DocumentRenderer.
 *
 * Existing widget fragment APIs are preserved.
 */
final class Renderer implements RendererInterface
{
    private readonly RenderPipeline $pipeline;

    private readonly DocumentRenderer $documentRenderer;

    public function __construct(
        ?RenderPipeline $pipeline = null,
        private readonly ?WidgetContext $defaultWidgetContext = null,
        ?DocumentRenderer $documentRenderer = null,
    ) {
        $this->pipeline = $pipeline ?? RenderPipeline::default();
        $this->documentRenderer = $documentRenderer ?? DocumentRenderer::usingPipeline(
            $this->pipeline,
            $this->defaultWidgetContext,
        );
    }

    public static function withPipeline(RenderPipeline $pipeline, ?WidgetContext $context = null): self
    {
        return new self($pipeline, $context);
    }

    public function render(DocumentInterface|WidgetInterface|string ...$nodes): string
    {
        $buffer = '';

        foreach ($nodes as $node) {
            if (is_string($node)) {
                $buffer .= $node;
                continue;
            }

            if ($node instanceof DocumentInterface) {
                $buffer .= $this->renderDocument($node);
                continue;
            }

            $buffer .= $this->renderWidget($node);
        }

        return $buffer;
    }

    public function renderDocument(DocumentInterface $document): string
    {
        return $this->documentRenderer->render($document);
    }

    /**
     * Render a single widget (alias retained for Milestone 1 callers).
     */
    public function renderComponent(WidgetInterface $component): string
    {
        return $this->renderWidget($component);
    }

    /**
     * Render a single widget through the runtime pipeline.
     */
    public function renderWidget(WidgetInterface $widget, ?RenderContext $context = null): string
    {
        $context ??= new RenderContext(
            widgetContext: $this->defaultWidgetContext ?? WidgetContext::defaults(),
        );
        $context->setRoot($widget);
        $context->setHtml('');

        $this->pipeline->run($context);

        return $context->html();
    }

    /**
     * Build a WidgetTree without rendering (inspection / DevTools).
     */
    public function tree(WidgetInterface $widget): WidgetTree
    {
        return WidgetTree::from($widget);
    }

    public function pipeline(): RenderPipeline
    {
        return $this->pipeline;
    }
}
