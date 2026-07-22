<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Badge;
use Serixa\Component\Button;
use Serixa\Component\Card;
use Serixa\Component\Checkbox;
use Serixa\Component\Container;
use Serixa\Component\Divider;
use Serixa\Component\Link;
use Serixa\Component\Select;
use Serixa\Component\Text;
use Serixa\Component\Textarea;
use Serixa\Rendering\Renderer;
use Serixa\Theme\ThemeManager;

/**
 * Snapshot-style regression renders for core widgets (light markup contracts).
 */
final class WidgetSnapshotTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testButtonVariantsRenderSemanticClasses(): void
    {
        $r = new Renderer();
        $this->assertStringContainsString('sx-btn-primary', $r->render(Button::make('A')->primary()));
        $this->assertStringContainsString('sx-btn-secondary', $r->render(Button::make('B')->secondary()));
        $this->assertStringContainsString('sx-btn-danger', $r->render(Button::make('C')->danger()));
        $this->assertStringContainsString('disabled', $r->render(Button::make('D')->disabled()));
    }

    public function testCardAcceptsWidgetInterfaceChildren(): void
    {
        $html = (new Renderer())->render(
            Card::make(Text::make('Hello'))->title('Title'),
        );
        $this->assertStringContainsString('<article', $html);
        $this->assertStringContainsString('Title', $html);
        $this->assertStringContainsString('Hello', $html);
    }

    public function testContainerAcceptsWidgetInterfaceChildren(): void
    {
        $html = (new Renderer())->render(Container::make(Text::make('Inside')));
        $this->assertStringContainsString('Inside', $html);
        $this->assertStringContainsString('w-full', $html);
    }

    public function testFormControlsRenderNativeElements(): void
    {
        $r = new Renderer();
        $this->assertStringContainsString('<textarea', $r->render(Textarea::make()->name('bio')));
        $this->assertStringContainsString('<select', $r->render(Select::make()->name('role')->options(['a' => 'A'])));
        $this->assertStringContainsString('type="checkbox"', $r->render(Checkbox::make()->name('ok')));
    }

    public function testLinkBadgeDividerSmoke(): void
    {
        $r = new Renderer();
        $this->assertStringContainsString('<a', $r->render(Link::make('Home')->href('/')));
        $this->assertStringContainsString('Badge', $r->render(Badge::make('Badge')));
        $this->assertStringStartsWith('<hr', $r->render(Divider::make()));
    }
}
