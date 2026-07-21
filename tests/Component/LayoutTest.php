<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Center;
use Serixa\Component\Column;
use Serixa\Component\Divider;
use Serixa\Component\Expanded;
use Serixa\Component\Padding;
use Serixa\Component\Row;
use Serixa\Component\SizedBox;
use Serixa\Component\Spacer;
use Serixa\Component\Stack;
use Serixa\Component\Text;
use Serixa\Theme\ThemeManager;

final class LayoutTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testRowJustifyAlignAndGap(): void
    {
        $html = Row::make(Text::make('A'), Text::make('B'))
            ->spaceBetween()
            ->alignCenter()
            ->gap(4)
            ->render();

        $this->assertStringContainsString('flex-row', $html);
        $this->assertStringContainsString('justify-between', $html);
        $this->assertStringContainsString('items-center', $html);
        $this->assertStringContainsString('gap-4', $html);
        $this->assertStringContainsString('A', $html);
        $this->assertStringContainsString('B', $html);
    }

    public function testColumnAcceptsArrayChildren(): void
    {
        $html = Column::make([
            Text::make('Top'),
            Text::make('Bottom'),
        ])->alignCenter()->gap(2)->render();

        $this->assertStringContainsString('flex-col', $html);
        $this->assertStringContainsString('items-center', $html);
        $this->assertStringContainsString('gap-2', $html);
        $this->assertStringContainsString('Top', $html);
        $this->assertStringContainsString('Bottom', $html);
    }

    public function testCenterUsesFlexCentering(): void
    {
        $html = Center::make(Text::make('Mid'))->render();

        $this->assertStringContainsString('items-center', $html);
        $this->assertStringContainsString('justify-center', $html);
        $this->assertStringContainsString('Mid', $html);
    }

    public function testPaddingFactories(): void
    {
        $this->assertStringContainsString('p-4', Padding::all(4)->render());
        $this->assertStringContainsString('px-2', Padding::x(2)->render());
        $this->assertStringContainsString('py-3', Padding::y(3)->render());
        $this->assertStringContainsString('pt-1', Padding::top(1)->render());
        $this->assertStringContainsString('pb-1', Padding::bottom(1)->render());
        $this->assertStringContainsString('pl-2', Padding::left(2)->render());
        $this->assertStringContainsString('pr-2', Padding::right(2)->render());
    }

    public function testSpacerAndExpanded(): void
    {
        $spacer = Spacer::make()->render();
        $expanded = Expanded::make(Text::make('Grow'))->render();

        $this->assertStringContainsString('flex-1', $spacer);
        $this->assertStringContainsString('flex-1', $expanded);
        $this->assertStringContainsString('min-w-0', $expanded);
        $this->assertStringContainsString('Grow', $expanded);
    }

    public function testSizedBoxHeightAndWidth(): void
    {
        $this->assertStringContainsString('h-20', SizedBox::height(20)->render());
        $this->assertStringContainsString('w-32', SizedBox::width(32)->render());
        $this->assertStringContainsString('w-10', SizedBox::height(8)->withWidth(10)->render());
        $this->assertStringContainsString('h-8', SizedBox::height(8)->withWidth(10)->render());
    }

    public function testStackIsRelativeLayerContext(): void
    {
        $html = Stack::make([
            Text::make('Back'),
            Text::make('Front'),
        ])->render();

        $this->assertStringContainsString('relative', $html);
        $this->assertStringContainsString('Back', $html);
        $this->assertStringContainsString('Front', $html);
    }

    public function testDividerRendersHr(): void
    {
        $html = Divider::make()->render();

        $this->assertStringStartsWith('<hr', $html);
        $this->assertStringContainsString('border-t', $html);
    }

    public function testFlutterStyleComposition(): void
    {
        $html = Center::make(
            Column::make([
                Text::make('Welcome'),
                SizedBox::height(20),
                Button::make('Continue')->primary()->large(),
            ]),
        )->render();

        $this->assertStringContainsString('Welcome', $html);
        $this->assertStringContainsString('h-20', $html);
        $this->assertStringContainsString('Continue', $html);
        $this->assertStringContainsString('bg-sky-600', $html);
        $this->assertStringContainsString('text-base', $html);
    }

    public function testButtonFluentAliasesAndRounded(): void
    {
        $html = Button::make('Go')->secondary()->small()->rounded()->render();

        $this->assertStringContainsString('bg-slate-100', $html);
        $this->assertStringContainsString('px-3', $html);
        $this->assertStringContainsString('rounded-full', $html);
    }

    public function testRowJustifyHelpers(): void
    {
        $this->assertStringContainsString('justify-start', Row::make()->justifyStart()->render());
        $this->assertStringContainsString('justify-center', Row::make()->justifyCenter()->render());
        $this->assertStringContainsString('justify-end', Row::make()->justifyEnd()->render());
        $this->assertStringContainsString('justify-around', Row::make()->spaceAround()->render());
    }
}
