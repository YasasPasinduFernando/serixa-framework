<?php

declare(strict_types=1);

namespace Serixa\Tests\Accessibility;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Serixa\Component\Alert;
use Serixa\Component\Button;
use Serixa\Component\Checkbox;
use Serixa\Component\Divider;
use Serixa\Component\Drawer;
use Serixa\Component\Dropdown;
use Serixa\Component\DropdownItem;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\Link;
use Serixa\Component\Modal;
use Serixa\Component\Progress;
use Serixa\Component\Spinner;
use Serixa\Component\Tab;
use Serixa\Component\Tabs;
use Serixa\Component\Text;
use Serixa\Component\Toast;
use Serixa\Component\Tooltip;
use Serixa\Contracts\WidgetInterface;
use Serixa\Rendering\Renderer;
use Serixa\Theme\ThemeManager;

final class AriaContractTest extends TestCase
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
    public static function ariaProvider(): array
    {
        return [
            'button' => [static fn () => Button::make('Go')->primary(), ['type="button"', 'Go']],
            'alert' => [static fn () => Alert::make()->title('A')->body('B'), ['role="alert"']],
            'modal' => [static fn () => Modal::make()->title('T')->body('B'), ['role="dialog"', 'aria-modal="true"']],
            'drawer' => [static fn () => Drawer::make()->title('D')->body('B'), ['role="dialog"', 'aria-modal="true"']],
            'dropdown' => [static fn () => Dropdown::make()->trigger(Button::make('M'))->items([DropdownItem::make('I')]), ['role="menu"', 'role="menuitem"', 'aria-expanded']],
            'tabs' => [static fn () => Tabs::make()->tab(Tab::make('One')->content(Text::make('P'))->active()), ['role="tablist"', 'role="tab"', 'role="tabpanel"', 'aria-selected']],
            'toast' => [static fn () => Toast::make()->message('Ok')->open(true), ['role="status"', 'aria-live']],
            'tooltip' => [static fn () => Tooltip::make()->text('Tip')->child(Button::make('?')), ['role="tooltip"', 'aria-describedby']],
            'progress' => [static fn () => Progress::make()->value(25)->label('Load'), ['role="progressbar"', 'aria-valuenow="25"', 'aria-label']],
            'spinner' => [static fn () => Spinner::make()->label('Wait'), ['role="status"', 'aria-label="Wait"']],
            'divider' => [static fn () => Divider::make()->label('Or'), ['role="separator"', 'aria-label="Or"']],
            'checkbox' => [static fn () => Checkbox::make()->name('c')->label('C'), ['type="checkbox"', 'C']],
            'input+label' => [static fn () => Label::make('Email')->for('e'), ['for="e"', 'Email']],
            'link' => [static fn () => Link::make('Home')->href('/'), ['href="/"', 'Home']],
            'input' => [static fn () => Input::make()->name('n')->id('nid'), ['name="n"', 'id="nid"']],
        ];
    }

    /**
     * @param callable(): WidgetInterface $factory
     * @param list<string> $needles
     */
    #[DataProvider('ariaProvider')]
    public function testAriaContracts(callable $factory, array $needles): void
    {
        $html = $this->renderer->render($factory());
        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $html);
        }
    }
}
