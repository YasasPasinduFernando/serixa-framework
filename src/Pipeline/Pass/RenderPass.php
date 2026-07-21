<?php

declare(strict_types=1);

namespace Serixa\Pipeline\Pass;

use Serixa\Exception\PipelineException;
use Serixa\Pipeline\RenderPassInterface;
use Serixa\Rendering\HtmlWriter;
use Serixa\Runtime\RenderContext;
use Serixa\Tree\LifecycleTreeVisitor;
use Serixa\Tree\TreeWalker;

/**
 * Invokes widget render() and writes HTML via HtmlWriter; finishes lifecycle.
 *
 * @internal
 */
final class RenderPass implements RenderPassInterface
{
    public function __construct(
        private readonly HtmlWriter $writer = new HtmlWriter(),
        private readonly TreeWalker $walker = new TreeWalker(),
    ) {
    }

    public function name(): string
    {
        return 'render';
    }

    public function handle(RenderContext $context): void
    {
        $root = $context->root();

        if ($root === null) {
            throw new PipelineException('RenderPass requires a root widget on RenderContext before HTML emission.');
        }

        $fragment = $root->render();
        $this->writer->write($context, $fragment);

        $tree = $context->tree();

        if ($tree !== null) {
            $this->walker->walk($tree->root(), LifecycleTreeVisitor::afterRender());
            $context->setMeta('lifecycle.phase', 'done');
        }

        $context->setMeta('pipeline.rendered', true);
    }
}
