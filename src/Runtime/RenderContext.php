<?php

declare(strict_types=1);

namespace Serixa\Runtime;

use Serixa\Contracts\WidgetInterface;
use Serixa\Tree\WidgetTree;

/**
 * Mutable per-render bag: widget context, tree, depth, SSR/debug flags, metadata.
 */
final class RenderContext
{
    private int $depth = 0;

    private ?WidgetTree $tree = null;

    private ?WidgetInterface $root = null;

    private string $html = '';

    /** @var array<string, mixed> */
    private array $metadata = [];

    private readonly string $renderId;

    public function __construct(
        private WidgetContext $widgetContext = new WidgetContext(),
        ?string $renderId = null,
        private bool $debug = false,
        private bool $ssr = true,
    ) {
        $this->renderId = $renderId ?? bin2hex(random_bytes(8));
    }

    public function widgetContext(): WidgetContext
    {
        return $this->widgetContext;
    }

    public function setWidgetContext(WidgetContext $context): void
    {
        $this->widgetContext = $context;
    }

    public function renderId(): string
    {
        return $this->renderId;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function isSsr(): bool
    {
        return $this->ssr;
    }

    public function setSsr(bool $ssr): void
    {
        $this->ssr = $ssr;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): void
    {
        $this->depth = max(0, $depth);
    }

    public function incrementDepth(): void
    {
        $this->depth++;
    }

    public function decrementDepth(): void
    {
        $this->depth = max(0, $this->depth - 1);
    }

    public function tree(): ?WidgetTree
    {
        return $this->tree;
    }

    public function setTree(WidgetTree $tree): void
    {
        $this->tree = $tree;
    }

    public function root(): ?WidgetInterface
    {
        return $this->root;
    }

    public function setRoot(WidgetInterface $root): void
    {
        $this->root = $root;
    }

    public function html(): string
    {
        return $this->html;
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    public function appendHtml(string $html): void
    {
        $this->html .= $html;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function setMeta(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }
}
