<?php

declare(strict_types=1);

namespace Serixa\Tree;

/**
 * Invokes widget lifecycle hooks while walking the tree.
 *
 * @internal
 */
final class LifecycleTreeVisitor implements TreeVisitor
{
    public function __construct(
        private readonly bool $mount = true,
        private readonly bool $beforeRender = false,
        private readonly bool $afterRender = false,
        private readonly bool $destroy = false,
    ) {
    }

    public static function mount(): self
    {
        return new self(mount: true);
    }

    public static function beforeRender(): self
    {
        return new self(mount: false, beforeRender: true);
    }

    public static function afterRender(): self
    {
        return new self(mount: false, afterRender: true);
    }

    public static function destroy(): self
    {
        return new self(mount: false, destroy: true);
    }

    public function enter(TreeNode $node): void
    {
        $widget = $node->widget();

        if ($widget === null) {
            return;
        }

        if ($this->mount) {
            $widget->beforeMount();
            $widget->mounted();
        }

        if ($this->beforeRender) {
            $widget->beforeRender();
        }
    }

    public function leave(TreeNode $node): void
    {
        $widget = $node->widget();

        if ($widget === null) {
            return;
        }

        if ($this->afterRender) {
            $widget->afterRender();
        }

        if ($this->destroy) {
            $widget->destroy();
        }
    }
}
