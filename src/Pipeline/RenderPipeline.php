<?php

declare(strict_types=1);

namespace Serixa\Pipeline;

use Serixa\Runtime\RenderContext;

/**
 * Ordered coordinator of render passes. Renderer delegates here.
 */
final class RenderPipeline
{
    /** @var list<RenderPassInterface> */
    private array $passes = [];

    /**
     * @param list<RenderPassInterface> $passes
     */
    public function __construct(array $passes = [])
    {
        foreach ($passes as $pass) {
            $this->add($pass);
        }
    }

    public function add(RenderPassInterface $pass): self
    {
        $this->passes[] = $pass;

        return $this;
    }

    /**
     * @return list<RenderPassInterface>
     */
    public function passes(): array
    {
        return $this->passes;
    }

    public function run(RenderContext $context): RenderContext
    {
        foreach ($this->passes as $pass) {
            $pass->handle($context);
        }

        return $context;
    }

    public static function default(): self
    {
        return new self([
            new Pass\BuildTreePass(),
            new Pass\ResolveContextPass(),
            new Pass\LifecyclePass(),
            new Pass\ThemeResolutionPass(),
            new Pass\AccessibilityPass(),
            new Pass\RenderPass(),
        ]);
    }
}
