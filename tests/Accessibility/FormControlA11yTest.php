<?php

declare(strict_types=1);

namespace Serixa\Tests\Accessibility;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Checkbox;
use Serixa\Component\Form;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\Link;
use Serixa\Component\Radio;
use Serixa\Component\Select;
use Serixa\Component\Table;
use Serixa\Component\TableBody;
use Serixa\Component\TableCell;
use Serixa\Component\TableHead;
use Serixa\Component\TableHeaderCell;
use Serixa\Component\TableRow;
use Serixa\Component\Textarea;
use Serixa\Contracts\WidgetInterface;
use Serixa\Rendering\Renderer;
use Serixa\Theme\ThemeManager;

final class FormControlA11yTest extends TestCase
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

    /**
     * @return array<string, array{0: callable(): WidgetInterface, 1: list<string>}>
     */
    public static function controlProvider(): array
    {
        return [
            'labeled-input' => [
                static fn () => Form::make(
                    Label::make('Name')->for('name'),
                    Input::make()->name('name')->id('name'),
                ),
                ['<form', 'for="name"', 'id="name"', 'name="name"'],
            ],
            'textarea' => [
                static fn () => Textarea::make()->name('notes')->id('notes')->attribute('aria-label', 'Notes'),
                ['<textarea', 'aria-label="Notes"', 'name="notes"'],
            ],
            'select' => [
                static fn () => Select::make()->name('role')->options(['a' => 'Admin'])->attribute('aria-label', 'Role'),
                ['<select', 'aria-label="Role"', 'Admin'],
            ],
            'checkbox' => [
                static fn () => Checkbox::make()->name('tos')->label('Accept terms'),
                ['type="checkbox"', 'Accept terms'],
            ],
            'radio' => [
                static fn () => Radio::make()->name('plan')->value('pro')->label('Pro'),
                ['type="radio"', 'Pro', 'value="pro"'],
            ],
            'button' => [
                static fn () => Button::make('Submit')->primary()->type('submit'),
                ['type="submit"', 'Submit'],
            ],
            'link' => [
                static fn () => Link::make('Docs')->href('/docs'),
                ['href="/docs"', 'Docs'],
            ],
            'table' => [
                static fn () => Table::make(
                    TableHead::make(TableRow::make(TableHeaderCell::make('Name'))),
                    TableBody::make(TableRow::make(TableCell::make('Ada'))),
                )->attribute('aria-label', 'People'),
                ['<table', 'aria-label="People"', 'Name', 'Ada'],
            ],
        ];
    }

    /**
     * @param callable(): WidgetInterface $factory
     * @param list<string> $needles
     */
    #[DataProvider('controlProvider')]
    public function testFormControlsExposeAccessibleMarkup(callable $factory, array $needles): void
    {
        $html = $this->renderer->render($factory());
        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $html);
        }
    }
}
