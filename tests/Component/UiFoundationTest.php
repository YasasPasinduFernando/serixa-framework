<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\AppBar;
use Serixa\Component\Button;
use Serixa\Component\Checkbox;
use Serixa\Component\Column;
use Serixa\Component\Divider;
use Serixa\Component\ErrorText;
use Serixa\Component\FieldGroup;
use Serixa\Component\Footer;
use Serixa\Component\Form;
use Serixa\Component\HelperText;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\Link;
use Serixa\Component\MainContent;
use Serixa\Component\PasswordInput;
use Serixa\Component\Radio;
use Serixa\Component\Scaffold;
use Serixa\Component\Select;
use Serixa\Component\Sidebar;
use Serixa\Component\SwitchControl;
use Serixa\Component\Text;
use Serixa\Component\Textarea;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Registry\WidgetRegistry;
use Serixa\Theme\ThemeManager;

final class UiFoundationTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testFormRendersMethodAndAction(): void
    {
        $html = Form::make(Input::make()->name('q'))
            ->method('POST')
            ->action('/login')
            ->render();

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('action="/login"', $html);
        $this->assertStringContainsString('name="q"', $html);
    }

    public function testLabelAssociatesWithControl(): void
    {
        $html = Label::make('Email')->for('email')->render();

        $this->assertStringContainsString('<label', $html);
        $this->assertStringContainsString('for="email"', $html);
        $this->assertStringContainsString('Email', $html);
    }

    public function testInputSupportsTypePlaceholderAndInvalid(): void
    {
        $html = Input::make()
            ->id('email')
            ->name('email')
            ->type('email')
            ->placeholder('You@example.com')
            ->invalid()
            ->describedBy('email-help')
            ->render();

        $this->assertStringContainsString('type="email"', $html);
        $this->assertStringContainsString('placeholder="You@example.com"', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('aria-describedby="email-help"', $html);
        $this->assertStringContainsString('border-rose-500', $html);
    }

    public function testPasswordInputIsAlwaysPasswordType(): void
    {
        $html = PasswordInput::make()->name('password')->required()->render();

        $this->assertStringContainsString('type="password"', $html);
        $this->assertStringContainsString('name="password"', $html);
        $this->assertStringContainsString('required', $html);
    }

    public function testTextareaRendersContentAndRows(): void
    {
        $html = Textarea::make('Hello')->name('bio')->rows(5)->render();

        $this->assertStringContainsString('<textarea', $html);
        $this->assertStringContainsString('rows="5"', $html);
        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('name="bio"', $html);
    }

    public function testCheckboxWithLabel(): void
    {
        $html = Checkbox::make()->name('remember')->label('Remember me')->checked()->render();

        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertStringContainsString('checked', $html);
        $this->assertStringContainsString('Remember me', $html);
        $this->assertStringContainsString('<label', $html);
    }

    public function testRadioRequiresValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Radio::make()->name('plan')->render();
    }

    public function testRadioWithLabel(): void
    {
        $html = Radio::make()->name('plan')->value('pro')->label('Pro')->checked()->render();

        $this->assertStringContainsString('type="radio"', $html);
        $this->assertStringContainsString('value="pro"', $html);
        $this->assertStringContainsString('Pro', $html);
    }

    public function testSwitchControlUsesSwitchRole(): void
    {
        $html = SwitchControl::make()->name('dark')->label('Dark mode')->checked()->render();

        $this->assertStringContainsString('role="switch"', $html);
        $this->assertStringContainsString('aria-checked="true"', $html);
        $this->assertStringContainsString('Dark mode', $html);
    }

    public function testSelectOptionsAndPlaceholder(): void
    {
        $html = Select::make()
            ->name('role')
            ->placeholder('Choose')
            ->options(['admin' => 'Admin', 'user' => 'User'])
            ->value('user')
            ->render();

        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('Choose', $html);
        $this->assertStringContainsString('value="admin"', $html);
        $this->assertStringContainsString('selected', $html);
        $this->assertStringContainsString('User', $html);
    }

    public function testFieldGroupHelperAndErrorText(): void
    {
        $html = FieldGroup::make(
            Label::make('Email')->for('email'),
            Input::make()->id('email')->name('email')->describedBy('email-help email-error'),
            HelperText::make('We never share your email.')->id('email-help'),
            ErrorText::make('Required.')->id('email-error'),
        )->render();

        $this->assertStringContainsString('We never share your email.', $html);
        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('Required.', $html);
        $this->assertStringContainsString('text-rose-600', $html);
    }

    public function testLinkActiveAndBlankTarget(): void
    {
        $html = Link::make('Docs')->href('/docs')->active()->target('_blank')->render();

        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('href="/docs"', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('noopener noreferrer', $html);
    }

    public function testScaffoldWithChromeWidgets(): void
    {
        $html = Scaffold::make()
            ->appBar(AppBar::make()->title('Dashboard')->actions(Button::make('Logout')->ghost()))
            ->sidebar(Sidebar::make(
                Link::make('Home')->href('/')->active(),
                Link::make('Settings')->href('/settings'),
            ))
            ->body(MainContent::make(Text::make('Hello')->as('h1')))
            ->footer(Footer::make('© 2026 SERIXA'))
            ->render();

        $this->assertStringContainsString('<header', $html);
        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('<aside', $html);
        $this->assertStringContainsString('role="navigation"', $html);
        $this->assertStringContainsString('<main', $html);
        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('<footer', $html);
        $this->assertStringContainsString('© 2026 SERIXA', $html);
        $this->assertStringContainsString('Logout', $html);
    }

    public function testDividerOrientationAndSpacing(): void
    {
        $horizontal = Divider::make()->spacing('sm')->label('Section')->render();
        $vertical = Divider::make()->vertical()->spacing('lg')->render();

        $this->assertStringContainsString('aria-orientation="horizontal"', $horizontal);
        $this->assertStringContainsString('aria-label="Section"', $horizontal);
        $this->assertStringContainsString('my-2', $horizontal);
        $this->assertStringContainsString('aria-orientation="vertical"', $vertical);
        $this->assertStringContainsString('border-l', $vertical);
        $this->assertStringContainsString('mx-6', $vertical);
    }

    public function testLoginFormCompositionExample(): void
    {
        $html = Form::make()
            ->method('POST')
            ->action('/login')
            ->child(
                Column::make([
                    Label::make('Email')->for('email'),
                    Input::make()->id('email')->name('email')->type('email')->placeholder('Enter your email'),
                    Label::make('Password')->for('password'),
                    PasswordInput::make()->id('password')->name('password'),
                    Checkbox::make()->name('remember')->label('Remember me'),
                    Button::make('Login')->primary()->type('submit'),
                ]),
            )
            ->render();

        $this->assertStringContainsString('action="/login"', $html);
        $this->assertStringContainsString('type="email"', $html);
        $this->assertStringContainsString('type="password"', $html);
        $this->assertStringContainsString('Remember me', $html);
        $this->assertStringContainsString('type="submit"', $html);
        $this->assertStringContainsString('Login', $html);
    }

    public function testRegistryContainsUiFoundationWidgets(): void
    {
        $registry = WidgetRegistry::withDefaults();

        foreach ([
            'form', 'label', 'input', 'passwordInput', 'textarea', 'checkbox', 'radio',
            'switch', 'select', 'fieldGroup', 'helperText', 'errorText', 'link',
            'appBar', 'sidebar', 'mainContent', 'footer',
        ] as $name) {
            $this->assertTrue($registry->has($name), "Missing registry entry: {$name}");
        }

        $this->assertSame(SwitchControl::class, $registry->classFor('switch'));
    }
}
