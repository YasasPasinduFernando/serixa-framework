<?php

declare(strict_types=1);

namespace Serixa\Tree;

use Serixa\Contracts\WidgetInterface;

/**
 * One node in a WidgetTree â€” either a widget or a text leaf.
 */
final class TreeNode
{
    /** @var list<TreeNode> */
    private array $children = [];

    private function __construct(
        private readonly WidgetInterface|string $value,
    ) {
    }

    public static function forWidget(WidgetInterface $widget): self
    {
        return new self($widget);
    }

    public static function forText(string $text): self
    {
        return new self($text);
    }

    public function addChild(TreeNode $child): self
    {
        $this->children[] = $child;

        return $this;
    }

    public function isWidget(): bool
    {
        return $this->value instanceof WidgetInterface;
    }

    public function widget(): ?WidgetInterface
    {
        return $this->value instanceof WidgetInterface ? $this->value : null;
    }

    public function text(): ?string
    {
        return is_string($this->value) ? $this->value : null;
    }

    /**
     * @return list<TreeNode>
     */
    public function children(): array
    {
        return $this->children;
    }
}
