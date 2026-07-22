<?php

declare(strict_types=1);

namespace Serixa\Tests\Dx;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Popover;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Theme\DefaultTheme;
use Serixa\Theme\ThemeManager;

final class ExceptionMessageTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testButtonVariantSuggestion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Did you mean "primary"/');
        Button::make('Save')->variant('primry');
    }

    public function testButtonVariantIncludesTip(): void
    {
        try {
            Button::make('Save')->variant('nope');
            $this->fail('Expected exception');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Allowed:', $e->getMessage());
            $this->assertStringContainsString('Tip:', $e->getMessage());
        }
    }

    public function testPopoverMessageIncludesExample(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Example: Popover::make/');
        Popover::make()->render();
    }

    public function testUnknownThemeComponentHint(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Tip: use DefaultTheme::classes/');
        (new DefaultTheme())->classes('not-a-real-component');
    }

    public function testUnknownTokenHint(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Tip: use ColorToken/');
        (new DefaultTheme())->token('color.notReal');
    }
}
