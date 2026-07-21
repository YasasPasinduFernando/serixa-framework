<?php

declare(strict_types=1);

namespace Serixa\Tree;

/**
 * Visitor over tree nodes (enter/leave for pre- and post-order work).
 */
interface TreeVisitor
{
    public function enter(TreeNode $node): void;

    public function leave(TreeNode $node): void;
}
