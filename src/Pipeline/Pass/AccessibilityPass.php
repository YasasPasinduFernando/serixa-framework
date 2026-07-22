<?php

declare(strict_types=1);

namespace Serixa\Pipeline\Pass;

use Serixa\Component\Button;
use Serixa\Component\Checkbox;
use Serixa\Component\Input;
use Serixa\Component\Select;
use Serixa\Component\Textarea;
use Serixa\Pipeline\RenderPassInterface;
use Serixa\Runtime\RenderContext;
use Serixa\Tree\TreeNode;

/**
 * Lightweight accessibility inventory during render (metadata only — no HTML mutation).
 *
 * Widgets already emit ARIA via AccessibilityBuilder; this pass records tree stats
 * for DevTools / inspectors without changing Widget APIs.
 *
 * @internal
 */
final class AccessibilityPass implements RenderPassInterface
{
    public function name(): string
    {
        return 'accessibility';
    }

    public function handle(RenderContext $context): void
    {
        $stats = [
            'buttons' => 0,
            'inputs' => 0,
            'textareas' => 0,
            'selects' => 0,
            'checkboxes' => 0,
            'nodes' => 0,
        ];

        $tree = $context->tree();
        if ($tree !== null) {
            $this->walk($tree->root(), $stats);
        }

        $context->setMeta('pipeline.accessibility', true);
        $context->setMeta('pipeline.accessibility.stats', $stats);
    }

    /**
     * @param array<string, int> $stats
     */
    private function walk(TreeNode $node, array &$stats): void
    {
        ++$stats['nodes'];
        $widget = $node->widget();

        if ($widget instanceof Button) {
            ++$stats['buttons'];
        } elseif ($widget instanceof Input) {
            ++$stats['inputs'];
        } elseif ($widget instanceof Textarea) {
            ++$stats['textareas'];
        } elseif ($widget instanceof Select) {
            ++$stats['selects'];
        } elseif ($widget instanceof Checkbox) {
            ++$stats['checkboxes'];
        }

        foreach ($node->children() as $child) {
            $this->walk($child, $stats);
        }
    }
}
