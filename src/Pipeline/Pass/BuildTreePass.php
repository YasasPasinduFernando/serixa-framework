<?php

declare(strict_types=1);

namespace Serixa\Pipeline\Pass;

use Serixa\Exception\PipelineException;
use Serixa\Pipeline\RenderPassInterface;
use Serixa\Runtime\RenderContext;
use Serixa\Tree\WidgetTree;

/**
 * Builds WidgetTree from the root widget.
 *
 * @internal
 */
final class BuildTreePass implements RenderPassInterface
{
    public function name(): string
    {
        return 'build_tree';
    }

    public function handle(RenderContext $context): void
    {
        $root = $context->root();

        if ($root === null) {
            throw new PipelineException('BuildTreePass: RenderContext has no root widget. Pass a WidgetInterface to Renderer::render().');
        }

        $context->setTree(WidgetTree::from($root));
        $context->setMeta('pipeline.tree_built', true);
    }
}
