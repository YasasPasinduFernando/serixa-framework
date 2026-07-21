<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Padding;
use Serixa\Component\Scaffold;
use Serixa\Component\Text;
use Serixa\Theme\ThemeManager;

final class DxCleanupTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testFluentPaddingCombinesAxesAndChild(): void
    {
        $html = Padding::make()
            ->x(6)
            ->y(4)
            ->child(Text::make('Inset'))
            ->render();

        $this->assertStringContainsString('px-6', $html);
        $this->assertStringContainsString('py-4', $html);
        $this->assertStringContainsString('Inset', $html);
    }

    public function testFluentPaddingAllAndSides(): void
    {
        $this->assertStringContainsString('p-4', Padding::make()->all(4)->render());

        $html = Padding::make()
            ->top(2)
            ->bottom(4)
            ->left(6)
            ->right(6)
            ->child(Text::make('Box'))
            ->render();

        $this->assertStringContainsString('pt-2', $html);
        $this->assertStringContainsString('pb-4', $html);
        $this->assertStringContainsString('pl-6', $html);
        $this->assertStringContainsString('pr-6', $html);
        $this->assertStringContainsString('Box', $html);
    }

    public function testDeprecatedStaticPaddingFactoriesStillWork(): void
    {
        $this->assertStringContainsString('p-4', Padding::all(4)->render());
        $this->assertStringContainsString('px-2', Padding::x(2, Text::make('X'))->render());
        $this->assertStringContainsString('py-3', Padding::y(3)->render());
        $this->assertStringContainsString('X', Padding::x(2, Text::make('X'))->render());
    }

    public function testScaffoldRendersSlotsAsSemanticRegions(): void
    {
        $html = Scaffold::make()
            ->appBar(Text::make('AppBar'))
            ->sidebar(Text::make('Side'))
            ->body(Text::make('Body'))
            ->footer(Text::make('Foot'))
            ->render();

        $this->assertStringContainsString('<header', $html);
        $this->assertStringContainsString('AppBar', $html);
        $this->assertStringContainsString('<aside', $html);
        $this->assertStringContainsString('Side', $html);
        $this->assertStringContainsString('Body', $html);
        $this->assertStringContainsString('<footer', $html);
        $this->assertStringContainsString('Foot', $html);
        $this->assertStringContainsString('flex-col', $html);
    }

    public function testScaffoldBodyOnly(): void
    {
        $html = Scaffold::make()->body(Text::make('Only'))->render();

        $this->assertStringContainsString('Only', $html);
        $this->assertStringNotContainsString('<header', $html);
        $this->assertStringNotContainsString('<aside', $html);
        $this->assertStringNotContainsString('<footer', $html);
    }
}
