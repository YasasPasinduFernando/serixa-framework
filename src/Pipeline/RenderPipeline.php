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

    private static ?self $defaultInstance = null;

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

    /**
     * Shared default pipeline (immutable pass list). Prefer this over allocating per render.
     */
    public static function default(): self
    {
        return self::$defaultInstance ??= new self([
            new Pass\BuildTreePass(),
            new Pass\ResolveContextPass(),
            new Pass\LifecyclePass(),
            new Pass\ThemeResolutionPass(),
            new Pass\AccessibilityPass(),
            new Pass\RenderPass(),
        ]);
    }

    /**
     * Reset the shared default (tests / custom environments).
     */
    public static function resetDefault(): void
    {
        self::$defaultInstance = null;
    }
}
