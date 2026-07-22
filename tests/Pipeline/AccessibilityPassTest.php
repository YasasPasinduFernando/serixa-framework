<?php

declare(strict_types=1);

namespace Serixa\Tests\Pipeline;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\Input;
use Serixa\Pipeline\RenderPipeline;
use Serixa\Runtime\RenderContext;
use Serixa\Runtime\WidgetContext;
use Serixa\Theme\ThemeManager;

final class AccessibilityPassTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        RenderPipeline::resetDefault();
        parent::tearDown();
    }

    public function testAccessibilityPassRecordsInteractiveStats(): void
    {
        $root = Column::make(
            Button::make('Save'),
            Input::make()->name('email'),
        );

        $context = new RenderContext(widgetContext: WidgetContext::defaults());
        $context->setRoot($root);
        RenderPipeline::default()->run($context);

        $this->assertTrue($context->metadata()['pipeline.accessibility'] ?? false);
        $stats = $context->metadata()['pipeline.accessibility.stats'] ?? [];
        $this->assertSame(1, $stats['buttons'] ?? 0);
        $this->assertSame(1, $stats['inputs'] ?? 0);
        $this->assertGreaterThan(0, $stats['nodes'] ?? 0);
    }

    public function testDefaultPipelineIsShared(): void
    {
        $this->assertSame(RenderPipeline::default(), RenderPipeline::default());
        RenderPipeline::resetDefault();
        $this->assertNotNull(RenderPipeline::default());
    }
}
