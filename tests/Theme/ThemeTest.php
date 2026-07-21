<?php

declare(strict_types=1);

namespace Serixa\Tests\Theme;

use PHPUnit\Framework\TestCase;
use Serixa\Contracts\ThemeInterface;
use Serixa\Theme\DefaultTheme;
use Serixa\Theme\ThemeManager;
use Serixa\Token\ColorToken;
use Serixa\Token\DesignToken;

final class ThemeTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testCurrentDefaultsToDefaultTheme(): void
    {
        $theme = ThemeManager::current();

        $this->assertInstanceOf(DefaultTheme::class, $theme);
        $this->assertSame($theme, ThemeManager::current());
    }

    public function testSetReplacesCurrentTheme(): void
    {
        $custom = new class () implements ThemeInterface {
            public function classes(string $component, array $state = []): array
            {
                return ['custom-' . $component];
            }

            public function token(DesignToken|string $token): array
            {
                $name = $token instanceof DesignToken ? $token->name() : $token;

                return ['token-' . $name];
            }

            public function buttonPrimary(): array
            {
                return $this->classes('button', ['variant' => 'primary']);
            }

            public function buttonSecondary(): array
            {
                return $this->classes('button', ['variant' => 'secondary']);
            }

            public function buttonDanger(): array
            {
                return $this->classes('button', ['variant' => 'danger']);
            }

            public function buttonGhost(): array
            {
                return $this->classes('button', ['variant' => 'ghost']);
            }
        };

        ThemeManager::set($custom);

        $this->assertSame(['custom-row'], ThemeManager::current()->classes('row'));
    }

    public function testButtonConvenienceMethodsDelegateToClasses(): void
    {
        $theme = new DefaultTheme();

        $this->assertContains('bg-sky-600', $theme->buttonPrimary());
        $this->assertContains('bg-slate-100', $theme->buttonSecondary());
        $this->assertContains('bg-red-600', $theme->buttonDanger());
        $this->assertContains('bg-transparent', $theme->buttonGhost());
    }

    public function testUnknownComponentThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DefaultTheme())->classes('unknown-widget');
    }

    public function testLayoutSpacingRejectsInvalidScale(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DefaultTheme())->classes('padding', ['all' => 99]);
    }

    public function testDesignTokensResolveToClasses(): void
    {
        $theme = new DefaultTheme();

        $this->assertContains('bg-sky-600', $theme->token(ColorToken::primary()));
        $this->assertContains('text-slate-500', $theme->token(ColorToken::textMuted()));
    }
}
