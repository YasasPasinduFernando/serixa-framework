<?php

declare(strict_types=1);

namespace Serixa\Tests\Styling;

use PHPUnit\Framework\TestCase;
use Serixa\Styling\ThemeCompiler;
use Serixa\Theme\DefaultTheme;
use Serixa\Theme\ThemeManager;
use Serixa\Token\ColorToken;
use Serixa\Token\TypographyToken;

final class ThemeTokenConsistencyTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testCompilerExposesCanonicalAndLegacyTypographyKeys(): void
    {
        $names = (new ThemeCompiler())->tokenNames();
        foreach ([
            'typography.body',
            'typography.heading',
            'typography.label',
            'typography.body.size',
            'typography.heading.size',
            'typography.label.size',
            'color.primary.hover',
            'color.onPrimary',
        ] as $name) {
            $this->assertContains($name, $names);
        }
    }

    public function testColorTokenFactoriesResolveInDefaultTheme(): void
    {
        $theme = new DefaultTheme();
        $this->assertNotEmpty($theme->token(ColorToken::primaryHover()));
        $this->assertNotEmpty($theme->token(ColorToken::secondaryHover()));
        $this->assertNotEmpty($theme->token(ColorToken::dangerHover()));
        $this->assertNotEmpty($theme->token(ColorToken::onPrimary()));
        $this->assertNotEmpty($theme->token(TypographyToken::body()));
    }

    public function testCompiledCssIncludesCanonicalTypographyVariables(): void
    {
        $css = '';
        foreach ((new ThemeCompiler())->compile() as $rule) {
            $css .= $rule->toCss();
        }

        $this->assertStringContainsString('--sx-typography-body:', $css);
        $this->assertStringContainsString('--sx-typography-heading:', $css);
        $this->assertStringContainsString('--sx-color-onPrimary:', $css);
        $this->assertStringContainsString('--sx-color-primary-hover:', $css);
        $this->assertStringContainsString('.dark{', $css);
        $this->assertStringContainsString('[data-theme="dark"]{', $css);
    }
}
