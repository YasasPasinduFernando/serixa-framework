<?php

declare(strict_types=1);

namespace Serixa\Tree;

use Serixa\Contracts\WidgetInterface;

/**
 * Immutable snapshot of a widget composition graph.
 *
 * Prepared for future diffing, inspection, and hot reload â€” not a virtual DOM.
 */
final class WidgetTree
{
    private function __construct(
        private readonly TreeNode $root,
    ) {
    }

    public static function from(WidgetInterface $root): self
    {
        return new self(self::buildNode($root));
    }

    public function root(): TreeNode
    {
        return $this->root;
    }

    private static function buildNode(WidgetInterface|string $node): TreeNode
    {
        if (is_string($node)) {
            return TreeNode::forText($node);
        }

        $treeNode = TreeNode::forWidget($node);

        foreach ($node->childrenForTree() as $child) {
            $treeNode->addChild(self::buildNode($child));
        }

        return $treeNode;
    }
}
