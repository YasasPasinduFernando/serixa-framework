<?php

declare(strict_types=1);

namespace Serixa\Document;

use Serixa\Contracts\DocumentInterface;
use Serixa\Contracts\WidgetInterface;
use Serixa\Pipeline\RenderPipeline;
use Serixa\Rendering\Renderer;
use Serixa\Runtime\RenderContext;
use Serixa\Runtime\WidgetContext;

/**
 * Coordinates Html â†’ Head (SEO + Assets) â†’ Body (widgets) into a complete HTML document.
 */
final class DocumentRenderer
{
    public function __construct(
        private readonly ?Renderer $fragmentRenderer = null,
        private readonly ?RenderPipeline $pipeline = null,
        private readonly ?WidgetContext $widgetContext = null,
    ) {
    }

    public static function make(?Renderer $fragmentRenderer = null): self
    {
        return new self($fragmentRenderer);
    }

    public static function usingPipeline(?RenderPipeline $pipeline = null, ?WidgetContext $context = null): self
    {
        return new self(null, $pipeline ?? RenderPipeline::default(), $context);
    }

    public static function withContext(WidgetContext $context): self
    {
        return self::usingPipeline(RenderPipeline::default(), $context);
    }

    public function render(DocumentInterface $document): string
    {
        $head = Head::make();
        $document->seo()->applyTo($head);
        $document->assets()->applyTo($head);

        $bodyInner = '';

        foreach ($document->bodyChildren() as $child) {
            $bodyInner .= $child instanceof WidgetInterface
                ? $this->renderFragment($child)
                : $child;
        }

        $body = $document instanceof Document
            ? $document->getBody()
            : Body::make(...$document->bodyChildren());

        $dir = $document instanceof Document ? $document->textDirection() : 'ltr';
        $html = Html::make($document->getLanguage(), $dir);

        return $html->wrap($head->render() . $body->wrap($bodyInner));
    }

    private function renderFragment(WidgetInterface $widget): string
    {
        if ($this->fragmentRenderer !== null) {
            return $this->fragmentRenderer->renderWidget($widget);
        }

        $context = new RenderContext(
            widgetContext: $this->widgetContext ?? WidgetContext::defaults(),
        );
        $context->setRoot($widget);
        $context->setHtml('');

        ($this->pipeline ?? RenderPipeline::default())->run($context);

        return $context->html();
    }
}
