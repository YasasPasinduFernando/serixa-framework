<?php

declare(strict_types=1);

namespace Serixa\Tests\Rendering;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Text;
use Serixa\Rendering\Renderer;

final class RendererTest extends TestCase
{
    public function testRenderConcatenatesComponents(): void
    {
        $renderer = new Renderer();

        $html = $renderer->render(
            Text::make('Hello')->as('span'),
            Button::make('Click'),
        );

        $this->assertStringContainsString('<span', $html);
        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('<button', $html);
        $this->assertStringContainsString('Click', $html);
    }

    public function testRenderComponentDelegatesToComponent(): void
    {
        $renderer = new Renderer();
        $button = Button::make('OK')->variant('secondary');

        $this->assertSame($button->render(), $renderer->renderComponent($button));
    }

    public function testTrustedHtmlStringsPassThrough(): void
    {
        $renderer = new Renderer();

        $html = $renderer->render(
            '<!-- fragment -->',
            Text::make('Body'),
        );

        $this->assertStringStartsWith('<!-- fragment -->', $html);
        $this->assertStringContainsString('Body', $html);
    }
}
