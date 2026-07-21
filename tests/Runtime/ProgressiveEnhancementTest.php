<?php

declare(strict_types=1);

namespace Serixa\Tests\Runtime;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Form;
use Serixa\Component\Input;
use Serixa\Component\PasswordInput;
use Serixa\Component\SearchField;
use Serixa\Component\Select;
use Serixa\Component\Textarea;
use Serixa\Document\Asset\AssetManager;
use Serixa\Event\EventRegistry;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Runtime\ClientRuntime;
use Serixa\Theme\ThemeManager;

final class ProgressiveEnhancementTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testDomEventAttributesRenderAsDataSerixa(): void
    {
        $button = Button::make('Save')->onClick('saveForm')->render();
        $this->assertStringContainsString('data-serixa-on-click="saveForm"', $button);

        $input = Input::make()->name('q')->onInput('search')->onChange('filter')->render();
        $this->assertStringContainsString('data-serixa-on-input="search"', $input);
        $this->assertStringContainsString('data-serixa-on-change="filter"', $input);

        $form = Form::make()->onSubmit('login')->render();
        $this->assertStringContainsString('data-serixa-on-submit="login"', $form);

        $search = SearchField::make()->name('q')->onInput('search')->onFocus('ready')->onBlur('idle')->render();
        $this->assertStringContainsString('data-serixa-on-input="search"', $search);
        $this->assertStringContainsString('data-serixa-on-focus="ready"', $search);
        $this->assertStringContainsString('data-serixa-on-blur="idle"', $search);
        $this->assertStringNotContainsString('data-serixa-on-input="search"', explode('<input', $search)[0]);

        $keys = Input::make()->onKeyDown('navDown')->onKeyUp('navUp')->render();
        $this->assertStringContainsString('data-serixa-on-keydown="navDown"', $keys);
        $this->assertStringContainsString('data-serixa-on-keyup="navUp"', $keys);
    }

    public function testEmptyEventHandlerRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Button::make('X')->onClick('');
    }

    public function testHtmlValidationAttributes(): void
    {
        $input = Input::make()
            ->name('age')
            ->type('number')
            ->required()
            ->readonly()
            ->min(1)
            ->max(100)
            ->minLength(1)
            ->maxLength(3)
            ->pattern('[0-9]+')
            ->autocomplete('off')
            ->render();

        $this->assertStringContainsString('required', $input);
        $this->assertStringContainsString('readonly', $input);
        $this->assertStringContainsString('min="1"', $input);
        $this->assertStringContainsString('max="100"', $input);
        $this->assertStringContainsString('minlength="1"', $input);
        $this->assertStringContainsString('maxlength="3"', $input);
        $this->assertStringContainsString('pattern="[0-9]+"', $input);
        $this->assertStringContainsString('autocomplete="off"', $input);
        $this->assertStringContainsString('aria-required="true"', $input);
        $this->assertStringContainsString('aria-readonly="true"', $input);

        $textarea = Textarea::make()->name('bio')->required()->maxLength(500)->render();
        $this->assertStringContainsString('maxlength="500"', $textarea);
        $this->assertStringContainsString('required', $textarea);

        $select = Select::make()->name('role')->required()->options(['a' => 'A'])->render();
        $this->assertStringContainsString('required', $select);

        $password = PasswordInput::make()->name('password')->minLength(6)->maxLength(255)->render();
        $this->assertStringContainsString('minlength="6"', $password);
        $this->assertStringContainsString('maxlength="255"', $password);
    }

    public function testEventRegistryMetadata(): void
    {
        $registry = new EventRegistry();
        $registry
            ->register('login', 'submit', 'Login form submit', 'form')
            ->register('search', 'input', 'Live search metadata', 'searchField')
            ->register('saveForm', 'click', 'Save button');

        $this->assertTrue($registry->has('login'));
        $this->assertSame('submit', $registry->get('login')['event']);
        $this->assertCount(1, $registry->forEvent('click'));
        $this->assertContains('search', $registry->names());
        $this->assertStringContainsString('login', $registry->toJson());

        $registry->unregister('login');
        $this->assertFalse($registry->has('login'));
    }

    public function testClientRuntimeScriptExistsAndRegistersAsset(): void
    {
        $path = ClientRuntime::scriptPath();
        $this->assertFileExists($path);
        $contents = (string) file_get_contents($path);
        $this->assertStringContainsString('Serixa', $contents);
        $this->assertStringContainsString('boot', $contents);
        $this->assertStringContainsString('data-serixa-on-', $contents);

        $assets = new AssetManager();
        ClientRuntime::register($assets);
        $collected = $assets->collect();
        $this->assertNotEmpty($collected);
        $this->assertSame('js', $collected[0]['type']);
        $this->assertStringContainsString('serixa.js', $collected[0]['path']);
    }
}
