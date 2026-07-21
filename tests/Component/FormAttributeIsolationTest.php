<?php

declare(strict_types=1);

namespace Serixa\Tests\Component;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Checkbox;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\Link;
use Serixa\Component\Radio;
use Serixa\Component\SearchField;
use Serixa\Component\SwitchControl;
use Serixa\Theme\ThemeManager;

final class FormAttributeIsolationTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testCheckboxDoesNotLeakAttributesToWrapperOrLabel(): void
    {
        $html = Checkbox::make()
            ->id('remember')
            ->name('remember')
            ->label('Remember me')
            ->required()
            ->attribute('data-test', 'widget')
            ->onChange('toggleRemember')
            ->class('user-class')
            ->render();

        $this->assertSame(1, substr_count($html, 'id="remember"'));
        $this->assertSame(1, substr_count($html, 'name="remember"'));
        $this->assertSame(1, substr_count($html, 'data-serixa-on-change="toggleRemember"'));
        $this->assertSame(1, substr_count($html, 'data-test="widget"'));
        $this->assertSame(1, substr_count($html, 'user-class'));
        $this->assertSame(1, preg_match_all('/(?:^|\s)required(?:\s|>|$)/', $html));
        $this->assertSame(1, substr_count($html, 'aria-required="true"'));

        $this->assertMatchesRegularExpression('/<label[^>]*\sfor="remember"/', $html);

        preg_match('/<div([^>]*)>/', $html, $wrap);
        $this->assertNotEmpty($wrap);
        $this->assertStringNotContainsString('id=', $wrap[1]);
        $this->assertStringNotContainsString('name=', $wrap[1]);
        $this->assertStringNotContainsString('data-serixa-on-', $wrap[1]);
        $this->assertStringNotContainsString('data-test=', $wrap[1]);
        $this->assertStringNotContainsString('user-class', $wrap[1]);

        preg_match('/<label([^>]*)>/', $html, $label);
        $this->assertNotEmpty($label);
        $this->assertStringContainsString('for="remember"', $label[1]);
        $this->assertStringNotContainsString('id=', $label[1]);
        $this->assertStringNotContainsString('name=', $label[1]);
        $this->assertStringNotContainsString('data-serixa-on-', $label[1]);
        $this->assertStringNotContainsString('data-test=', $label[1]);
    }

    public function testRadioDoesNotLeakAttributesToWrapperOrLabel(): void
    {
        $html = Radio::make()
            ->id('plan-pro')
            ->name('plan')
            ->value('pro')
            ->label('Pro')
            ->onChange('pickPlan')
            ->attribute('data-test', 'radio')
            ->render();

        $this->assertSame(1, substr_count($html, 'id="plan-pro"'));
        $this->assertSame(1, substr_count($html, 'data-serixa-on-change="pickPlan"'));
        $this->assertSame(1, substr_count($html, 'data-test="radio"'));
        $this->assertMatchesRegularExpression('/<label[^>]*\sfor="plan-pro"/', $html);

        preg_match('/<div([^>]*)>/', $html, $wrap);
        $this->assertStringNotContainsString('id=', $wrap[1]);
        $this->assertStringNotContainsString('data-serixa-on-', $wrap[1]);

        preg_match('/<label([^>]*)>/', $html, $label);
        $this->assertStringNotContainsString('id=', $label[1]);
        $this->assertStringNotContainsString('name=', $label[1]);
    }

    public function testSwitchControlDoesNotLeakAttributesToWrapperOrLabel(): void
    {
        $html = SwitchControl::make()
            ->id('digests')
            ->name('digests')
            ->label('Email digests')
            ->onChange('toggleDigests')
            ->attribute('data-test', 'switch')
            ->render();

        $this->assertSame(1, substr_count($html, 'id="digests"'));
        $this->assertSame(1, substr_count($html, 'role="switch"'));
        $this->assertSame(1, substr_count($html, 'data-serixa-on-change="toggleDigests"'));

        preg_match('/<div([^>]*)>/', $html, $wrap);
        $this->assertStringNotContainsString('id=', $wrap[1]);
        $this->assertStringNotContainsString('role="switch"', $wrap[1]);
        $this->assertStringNotContainsString('data-serixa-on-', $wrap[1]);

        preg_match('/<label([^>]*)>/', $html, $label);
        $this->assertStringContainsString('for="digests"', $label[1]);
        $this->assertStringNotContainsString('id=', $label[1]);
    }

    public function testSearchFieldKeepsEventsAndIdOnInputOnly(): void
    {
        $html = SearchField::make()
            ->id('q')
            ->name('q')
            ->onInput('search')
            ->attribute('data-test', 'search')
            ->class('search-user')
            ->icon('🔍')
            ->render();

        $this->assertSame(1, substr_count($html, 'id="q"'));
        $this->assertSame(1, substr_count($html, 'name="q"'));
        $this->assertSame(1, substr_count($html, 'data-serixa-on-input="search"'));
        $this->assertSame(1, substr_count($html, 'data-test="search"'));
        $this->assertSame(1, substr_count($html, 'role="search"'));
        $this->assertSame(1, substr_count($html, 'role="searchbox"'));

        preg_match('/<div([^>]*)>/', $html, $wrap);
        $this->assertStringContainsString('role="search"', $wrap[1]);
        $this->assertStringNotContainsString('id=', $wrap[1]);
        $this->assertStringNotContainsString('name=', $wrap[1]);
        $this->assertStringNotContainsString('data-serixa-on-', $wrap[1]);
        $this->assertStringNotContainsString('data-test=', $wrap[1]);
        $this->assertStringNotContainsString('search-user', $wrap[1]);

        preg_match('/<span([^>]*)>/', $html, $icon);
        $this->assertStringNotContainsString('id=', $icon[1]);
        $this->assertStringNotContainsString('data-serixa-on-', $icon[1]);

        preg_match('/<input([^>]*)\/>/', $html, $input);
        $this->assertStringContainsString('id="q"', $input[1]);
        $this->assertStringContainsString('data-serixa-on-input="search"', $input[1]);
        $this->assertStringContainsString('search-user', $input[1]);
    }

    public function testInputRendersControlAttributesOnce(): void
    {
        $html = Input::make()
            ->id('email')
            ->name('email')
            ->type('email')
            ->required()
            ->readonly()
            ->disabled()
            ->minLength(3)
            ->maxLength(255)
            ->autocomplete('email')
            ->onInput('search')
            ->render();

        $this->assertSame(1, substr_count($html, 'id="email"'));
        $this->assertSame(1, substr_count($html, 'name="email"'));
        $this->assertSame(1, preg_match_all('/(?:^|\s)required(?:\s|>|$)/', $html));
        $this->assertSame(1, preg_match_all('/(?:^|\s)readonly(?:\s|>|$)/', $html));
        $this->assertSame(1, preg_match_all('/(?:^|\s)disabled(?:\s|>|$)/', $html));
        $this->assertSame(1, substr_count($html, 'minlength="3"'));
        $this->assertSame(1, substr_count($html, 'maxlength="255"'));
        $this->assertSame(1, substr_count($html, 'autocomplete="email"'));
        $this->assertSame(1, substr_count($html, 'aria-required="true"'));
        $this->assertSame(1, substr_count($html, 'aria-readonly="true"'));
        $this->assertSame(1, substr_count($html, 'aria-disabled="true"'));
        $this->assertSame(1, substr_count($html, 'data-serixa-on-input="search"'));
    }

    public function testLabelAssociatesWithoutLeakingWidgetIdOntoOtherNodes(): void
    {
        $label = Label::make('Email')->for('email')->id('email-label')->render();
        $this->assertSame(1, substr_count($label, 'id="email-label"'));
        $this->assertSame(1, substr_count($label, 'for="email"'));
        $this->assertStringContainsString('Email', $label);

        $input = Input::make()->id('email')->name('email')->render();
        $this->assertStringContainsString('id="email"', $input);
    }

    public function testLinkRendersSingleHrefAndEscapesLabel(): void
    {
        $html = Link::make('<script>x</script>')->href('/safe')->active()->render();
        $this->assertSame(1, substr_count($html, 'href="/safe"'));
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>x</script>', $html);
        $this->assertSame(1, substr_count($html, 'aria-current="page"'));
    }
}
