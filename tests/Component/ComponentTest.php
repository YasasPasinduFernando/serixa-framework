<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Card;
use Serixa\Component\Container;
use Serixa\Component\Text;

final class ComponentTest extends TestCase
{
    public function testTextRendersEscapedContentWithSemanticClasses(): void
    {
        $html = Text::make('Hello <world>')
            ->as('h1')
            ->size('xl')
            ->weight('bold')
            ->tone('primary')
            ->id('hero')
            ->render();

        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('id="hero"', $html);
        $this->assertStringContainsString('text-xl', $html);
        $this->assertStringContainsString('font-bold', $html);
        $this->assertStringContainsString('text-sky-700', $html);
        $this->assertStringContainsString('Hello &lt;world&gt;', $html);
        $this->assertStringContainsString('</h1>', $html);
    }

    public function testTextRejectsInvalidTag(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Text::make('x')->as('h1 onclick=alert(1)');
    }

    public function testButtonSupportsChainingAndAccessibility(): void
    {
        $html = Button::make('Save')
            ->type('submit')
            ->variant('danger')
            ->size('lg')
            ->disabled()
            ->class('custom-btn')
            ->render();

        $this->assertStringStartsWith('<button', $html);
        $this->assertStringContainsString('type="submit"', $html);
        $this->assertStringContainsString('disabled', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringContainsString('sx-btn-danger', $html);
        $this->assertStringContainsString('custom-btn', $html);
        $this->assertStringContainsString('Save</button>', $html);
    }

    public function testButtonEscapesLabel(): void
    {
        $html = Button::make('<img>')->render();

        $this->assertStringContainsString('&lt;img&gt;', $html);
        $this->assertStringNotContainsString('<img>', $html);
    }

    public function testContainerRendersChildrenAndLayoutClasses(): void
    {
        $html = Container::make()
            ->maxWidth('md')
            ->padding('sm')
            ->child(Text::make('Inside')->as('span'))
            ->child(Button::make('Go'))
            ->render();

        $this->assertStringStartsWith('<div', $html);
        $this->assertStringContainsString('max-w-screen-md', $html);
        $this->assertStringContainsString('px-4', $html);
        $this->assertStringContainsString('<span', $html);
        $this->assertStringContainsString('Inside', $html);
        $this->assertStringContainsString('<button', $html);
        $this->assertStringEndsWith('</div>', $html);
    }

    public function testContainerAcceptsConstructorChildren(): void
    {
        $html = Container::make(
            Text::make('A'),
            Text::make('B'),
        )->render();

        $this->assertStringContainsString('A', $html);
        $this->assertStringContainsString('B', $html);
    }

    public function testCardRendersArticleWithTitleAndAria(): void
    {
        $html = Card::make()
            ->id('profile')
            ->title('Profile')
            ->padding('lg')
            ->child(Text::make('Details'))
            ->render();

        $this->assertStringStartsWith('<article', $html);
        $this->assertStringContainsString('id="profile"', $html);
        $this->assertStringContainsString('aria-labelledby="profile-title"', $html);
        $this->assertStringContainsString('id="profile-title"', $html);
        $this->assertStringContainsString('Profile', $html);
        $this->assertStringContainsString('Details', $html);
        $this->assertStringContainsString('p-8', $html);
        $this->assertStringEndsWith('</article>', $html);
    }

    public function testNestedComposition(): void
    {
        $html = Container::make(
            Card::make()
                ->title('Welcome')
                ->child(Text::make('Build UI in PHP.'))
                ->child(
                    Button::make('Get started')->variant('primary'),
                ),
        )->render();

        $this->assertStringContainsString('<div', $html);
        $this->assertStringContainsString('<article', $html);
        $this->assertStringContainsString('Welcome', $html);
        $this->assertStringContainsString('Get started', $html);
    }

    public function testRawStringChildrenAreEscaped(): void
    {
        $html = Container::make('<b>raw</b>')->render();

        $this->assertStringContainsString('&lt;b&gt;raw&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>raw</b>', $html);
    }
}
