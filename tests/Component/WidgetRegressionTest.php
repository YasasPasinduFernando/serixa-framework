<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Serixa\Component\Alert;
use Serixa\Component\Avatar;
use Serixa\Component\Button;
use Serixa\Component\Center;
use Serixa\Component\Chip;
use Serixa\Component\Column;
use Serixa\Component\Expanded;
use Serixa\Component\Footer;
use Serixa\Component\Row;
use Serixa\Component\Spacer;
use Serixa\Component\Stack;
use Serixa\Component\Text;
use Serixa\Rendering\Renderer;
use Serixa\Theme\ThemeManager;

final class WidgetRegressionTest extends TestCase
{
    private Renderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = new Renderer();
    }

    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public static function buttonSizeProvider(): array
    {
        return [
            'sm' => ['small', 'sx-btn-sm'],
            'lg' => ['large', 'sx-btn-lg'],
        ];
    }

    #[DataProvider('buttonSizeProvider')]
    public function testButtonSizes(string $method, string $class): void
    {
        $html = $this->renderer->render(Button::make('X')->{$method}());
        $this->assertStringContainsString('sx-btn', $html);
        $this->assertStringContainsString($class, $html);
    }

    public function testDefaultButtonHasMdSemanticClass(): void
    {
        $html = $this->renderer->render(Button::make('X')->primary());
        $this->assertStringContainsString('sx-btn-md', $html);
    }

    public function testLayoutPrimitivesDoNotForceWidth(): void
    {
        foreach ([
            Column::make(Text::make('c')),
            Row::make(Text::make('r')),
            Center::make(Text::make('m')),
            Stack::make(Text::make('s')),
        ] as $widget) {
            $html = $this->renderer->render($widget);
            $this->assertStringNotContainsString('w-full', $html);
        }
    }

    public function testFillAndExpanded(): void
    {
        $this->assertStringContainsString('w-full', $this->renderer->render(Column::make()->fill()));
        $this->assertStringContainsString('flex-1', $this->renderer->render(Expanded::make(Text::make('e'))));
        $this->assertStringContainsString('flex-1', $this->renderer->render(Spacer::make()));
    }

    public function testAlertAvatarChipFooterSmoke(): void
    {
        $this->assertNotSame('', $this->renderer->render(Alert::make()->title('Note')->body('Hello')));
        $this->assertNotSame('', $this->renderer->render(Avatar::make()->initials('AL')));
        $this->assertNotSame('', $this->renderer->render(Chip::make('Tag')));
        $this->assertNotSame('', $this->renderer->render(Footer::make(Text::make('©'))));
    }
}
