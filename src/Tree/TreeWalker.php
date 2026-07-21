<?php

declare(strict_types=1);

namespace Serixa\Tree;

/**
 * Walks a WidgetTree in depth-first order.
 */
final class TreeWalker
{
    public function walk(TreeNode $node, TreeVisitor $visitor): void
    {
        $visitor->enter($node);

        foreach ($node->children() as $child) {
            $this->walk($child, $visitor);
        }

        $visitor->leave($node);
    }
}
