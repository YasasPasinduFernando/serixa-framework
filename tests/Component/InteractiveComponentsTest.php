<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Accordion;
use Serixa\Component\AccordionItem;
use Serixa\Component\Alert;
use Serixa\Component\Badge;
use Serixa\Component\Button;
use Serixa\Component\Chip;
use Serixa\Component\Drawer;
use Serixa\Component\Dropdown;
use Serixa\Component\DropdownItem;
use Serixa\Component\DropdownMenu;
use Serixa\Component\Modal;
use Serixa\Component\Popover;
use Serixa\Component\Progress;
use Serixa\Component\Skeleton;
use Serixa\Component\Spinner;
use Serixa\Component\Tab;
use Serixa\Component\TabPanel;
use Serixa\Component\Tabs;
use Serixa\Component\Text;
use Serixa\Component\Toast;
use Serixa\Component\Tooltip;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Registry\WidgetRegistry;
use Serixa\Runtime\ClientRuntime;
use Serixa\Theme\ThemeManager;

final class InteractiveComponentsTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testModalRendersDialogSemantics(): void
    {
        $closed = Modal::make()
            ->title('Confirm')
            ->body(Text::make('Are you sure?'))
            ->footer(Button::make('OK')->primary())
            ->open(false)
            ->render();

        $this->assertStringContainsString('role="dialog"', $closed);
        $this->assertStringContainsString('aria-modal="true"', $closed);
        $this->assertStringContainsString('data-serixa-modal', $closed);
        $this->assertStringContainsString('hidden', $closed);
        $this->assertStringContainsString('aria-labelledby', $closed);
        $this->assertStringContainsString('Confirm', $closed);
        $this->assertStringContainsString('Are you sure?', $closed);

        $labelled = Modal::make()->body('Body')->ariaLabel('Delete confirmation')->render();
        $this->assertStringContainsString('aria-label="Delete confirmation"', $labelled);

        $open = Modal::make()->title('Open')->body('Hi')->open(true)->render();
        $this->assertStringContainsString('data-serixa-open="true"', $open);
        $this->assertDoesNotMatchRegularExpression('/data-serixa-modal[^>]*\shidden[\s>]/', $open);
    }

    public function testDrawerRendersPanelSemantics(): void
    {
        $html = Drawer::make()
            ->side('right')
            ->title('Filters')
            ->body(Text::make('Panel body'))
            ->open(false)
            ->render();

        $this->assertStringContainsString('data-serixa-drawer', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('Filters', $html);
        $this->assertStringContainsString('hidden', $html);

        $labelled = Drawer::make()->body('Body')->ariaLabel('Filters panel')->render();
        $this->assertStringContainsString('aria-label="Filters panel"', $labelled);
    }

    public function testDropdownAndDropdownItemSemantics(): void
    {
        $trigger = Button::make('Menu');
        $html = Dropdown::make()
            ->trigger($trigger)
            ->items([
                DropdownItem::make('Profile')->href('/profile'),
                DropdownItem::make('Logout'),
            ])
            ->open(false)
            ->render();

        $this->assertStringContainsString('data-serixa-dropdown', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('role="menu"', $html);
        $this->assertStringContainsString('role="menuitem"', $html);
        $this->assertStringContainsString('Profile', $html);
        $this->assertStringContainsString('href="/profile"', $html);
        $this->assertMatchesRegularExpression('/<button[^>]+data-serixa-dropdown-trigger[^>]+aria-haspopup="menu"/', $html);
        $this->assertStringNotContainsString('data-serixa-dropdown-trigger', $trigger->render());
    }

    public function testDropdownMenuStandaloneSemantics(): void
    {
        $html = DropdownMenu::make(
            DropdownItem::make('Edit'),
            DropdownItem::make('Delete')->danger(),
        )->render();

        $this->assertStringContainsString('role="menu"', $html);
        $this->assertStringContainsString('data-serixa-dropdown-menu', $html);
        $this->assertStringContainsString('role="menuitem"', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Delete', $html);
    }

    public function testPopoverSemanticsAndValidation(): void
    {
        $trigger = Button::make('More');
        $html = Popover::make()
            ->trigger($trigger)
            ->content(Text::make('Details'))
            ->open(false)
            ->render();

        $this->assertStringContainsString('data-serixa-popover', $html);
        $this->assertStringContainsString('data-serixa-popover-trigger', $html);
        $this->assertStringContainsString('data-serixa-popover-panel', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('Details', $html);
        $this->assertStringContainsString('hidden', $html);
        $this->assertStringContainsString('aria-label="Popover"', $html);
        $this->assertMatchesRegularExpression('/<button[^>]+data-serixa-popover-trigger[^>]+aria-expanded="false"/', $html);
        $this->assertStringNotContainsString('data-serixa-popover-trigger', $trigger->render());

        $this->expectException(InvalidArgumentException::class);
        Popover::make()->trigger(Button::make('x'))->render();
    }

    public function testTabPanelRequiresContent(): void
    {
        $html = TabPanel::make()->content(Text::make('Panel body'))->render();
        $this->assertStringContainsString('Panel body', $html);

        $this->expectException(InvalidArgumentException::class);
        TabPanel::make()->render();
    }

    public function testTabsAndTabSemantics(): void
    {
        $html = Tabs::make()
            ->tab(Tab::make('General')->content(Text::make('General panel'))->active())
            ->tab(Tab::make('Security')->content(Text::make('Security panel')))
            ->render();

        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('role="tab"', $html);
        $this->assertStringContainsString('role="tabpanel"', $html);
        $this->assertStringContainsString('aria-selected="true"', $html);
        $this->assertStringContainsString('aria-selected="false"', $html);
        $this->assertStringContainsString('General panel', $html);
        $this->assertStringContainsString('Security panel', $html);
        $this->assertStringContainsString('data-serixa-tabs', $html);
    }

    public function testAccordionAndAccordionItemSemantics(): void
    {
        $html = Accordion::make()
            ->item(
                AccordionItem::make()
                    ->title('FAQ')
                    ->content(Text::make('Answer'))
                    ->open(false),
            )
            ->item(
                AccordionItem::make()
                    ->title('More')
                    ->content(Text::make('Details'))
                    ->open(true),
            )
            ->render();

        $this->assertStringContainsString('data-serixa-accordion', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('aria-expanded="true"', $html);
        $this->assertStringContainsString('aria-controls', $html);
        $this->assertStringContainsString('FAQ', $html);
        $this->assertStringContainsString('Answer', $html);
    }

    public function testTooltipSemantics(): void
    {
        $html = Tooltip::make()
            ->text('More info')
            ->child(Button::make('?')->ghost())
            ->render();

        $this->assertStringContainsString('role="tooltip"', $html);
        $this->assertStringContainsString('aria-describedby', $html);
        $this->assertStringContainsString('More info', $html);
        $this->assertStringContainsString('data-serixa-tooltip', $html);
    }

    public function testToastSemantics(): void
    {
        $html = Toast::make()->message('Saved')->tone('success')->open(true)->render();

        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('aria-live="polite"', $html);
        $this->assertStringContainsString('Saved', $html);
        $this->assertStringContainsString('data-serixa-toast', $html);
    }

    public function testAlertBadgeChipSpinnerProgressSkeletonBasics(): void
    {
        $alert = Alert::make()->tone('warning')->title('Caution')->body('Check settings')->render();
        $this->assertStringContainsString('role="alert"', $alert);
        $this->assertStringContainsString('Caution', $alert);
        $this->assertStringContainsString('data-serixa-alert', $alert);

        $badge = Badge::make('Admin')->color('primary')->render();
        $this->assertStringContainsString('Admin', $badge);

        $chip = Chip::make('PHP')->dismissible()->render();
        $this->assertStringContainsString('PHP', $chip);
        $this->assertStringContainsString('data-serixa-chip', $chip);
        $this->assertStringContainsString('data-serixa-chip-dismiss', $chip);

        $spinner = Spinner::make()->size('md')->label('Loading')->render();
        $this->assertStringContainsString('role="status"', $spinner);
        $this->assertStringContainsString('aria-label="Loading"', $spinner);
        $this->assertStringContainsString('data-serixa-spinner', $spinner);

        $progress = Progress::make()->value(40)->max(100)->label('Upload')->render();
        $this->assertStringContainsString('role="progressbar"', $progress);
        $this->assertStringContainsString('aria-valuenow="40"', $progress);
        $this->assertStringContainsString('data-serixa-progress', $progress);

        $clamped = Progress::make()->value(120)->max(100)->render();
        $this->assertStringContainsString('aria-valuenow="100"', $clamped);
        $this->assertStringContainsString('style="width: 100%"', $clamped);

        $skeleton = Skeleton::make()->lines(3)->render();
        $this->assertStringContainsString('data-serixa-skeleton', $skeleton);
        $this->assertStringContainsString('role="status"', $skeleton);
    }

    public function testProgressRejectsNonFiniteValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Progress::make()->value(INF);
    }

    public function testRegistryContainsInteractiveWidgets(): void
    {
        $registry = WidgetRegistry::withDefaults();

        foreach ([
            'modal', 'drawer', 'popover', 'tooltip', 'toast', 'alert', 'badge', 'chip',
            'spinner', 'progress', 'skeleton', 'dropdown', 'dropdownMenu', 'dropdownItem',
            'accordion', 'accordionItem', 'tabs', 'tab', 'tabPanel',
        ] as $name) {
            $this->assertTrue($registry->has($name), "Missing: {$name}");
        }
    }

    public function testClientRuntimeScriptContainsModules(): void
    {
        $path = ClientRuntime::scriptPath();
        $this->assertFileExists($path);
        $contents = (string) file_get_contents($path);
        $this->assertStringContainsString('module(', $contents);
        $this->assertStringContainsString('0.11.2', $contents);
        $this->assertStringContainsString('modal', $contents);
    }
}
