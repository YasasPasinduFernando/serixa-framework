<?php

declare(strict_types=1);

namespace Serixa\Tests\Theme;

use PHPUnit\Framework\TestCase;
use Serixa\Styling\ThemeCompiler;
use Serixa\Theme\DefaultTheme;
use Serixa\Theme\ThemeManager;
use Serixa\Token\BorderToken;
use Serixa\Token\BreakpointToken;
use Serixa\Token\ColorToken;
use Serixa\Token\OpacityToken;
use Serixa\Token\RadiusToken;
use Serixa\Token\ShadowToken;
use Serixa\Token\SpacingToken;
use Serixa\Token\TransitionToken;
use Serixa\Token\TypographyToken;
use Serixa\Token\ZIndexToken;

final class ThemeExhaustiveTokenTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testEveryCompilerTokenEmitsCssVariable(): void
    {
        $compiler = new ThemeCompiler();
        $css = '';
        foreach ($compiler->compile() as $rule) {
            $css .= $rule->toCss();
        }

        foreach ($compiler->tokenNames() as $name) {
            $var = '--sx-' . str_replace('.', '-', $name);
            $this->assertStringContainsString($var . ':', $css, $name);
        }
    }

    public function testDefaultThemeResolvesAllPublicTokenFactories(): void
    {
        $theme = new DefaultTheme();
        $tokens = [
            ColorToken::primary(),
            ColorToken::primaryHover(),
            ColorToken::secondary(),
            ColorToken::secondaryHover(),
            ColorToken::danger(),
            ColorToken::dangerHover(),
            ColorToken::onPrimary(),
            ColorToken::surface(),
            ColorToken::background(),
            ColorToken::textPrimary(),
            ColorToken::textMuted(),
            SpacingToken::small(),
            SpacingToken::medium(),
            SpacingToken::large(),
            RadiusToken::small(),
            RadiusToken::medium(),
            RadiusToken::large(),
            RadiusToken::full(),
            ShadowToken::small(),
            ShadowToken::medium(),
            ShadowToken::large(),
            TypographyToken::body(),
            TypographyToken::heading(),
            TypographyToken::label(),
            TypographyToken::muted(),
            BorderToken::subtle(),
            BorderToken::strong(),
            BorderToken::focus(),
            OpacityToken::disabled(),
            OpacityToken::muted(),
            OpacityToken::overlay(),
            ZIndexToken::base(),
            ZIndexToken::dropdown(),
            ZIndexToken::sticky(),
            ZIndexToken::modal(),
            ZIndexToken::toast(),
            BreakpointToken::sm(),
            BreakpointToken::md(),
            BreakpointToken::lg(),
            BreakpointToken::xl(),
            TransitionToken::fast(),
            TransitionToken::normal(),
            TransitionToken::slow(),
        ];

        foreach ($tokens as $token) {
            $this->assertNotEmpty($theme->token($token), $token->name());
        }
    }

    public function testEveryThemeComponentKeyResolvesNonEmptyClasses(): void
    {
        $src = (string) file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Theme' . DIRECTORY_SEPARATOR . 'DefaultTheme.php');
        $start = strpos($src, 'return match ($component) {');
        $this->assertNotFalse($start);
        $end = strpos($src, 'default => throw new InvalidArgumentException', $start);
        $this->assertNotFalse($end);
        $matchBlock = substr($src, $start, $end - $start);
        preg_match_all("/'\\s*([^']+)\\s*'\\s*=>\\s*\\\$this->/", $matchBlock, $matches);
        $keys = array_values(array_unique($matches[1]));
        $this->assertGreaterThan(120, count($keys));
        $this->assertNotContains('default', $keys);

        $theme = new DefaultTheme();
        $nonEmptyKeys = 0;
        foreach ($keys as $key) {
            $classes = $theme->classes($key);
            $this->assertIsArray($classes, $key);
            foreach ($classes as $class) {
                $this->assertIsString($class, $key);
                $this->assertNotSame('', trim($class), $key);
            }
            if ($classes !== []) {
                ++$nonEmptyKeys;
            }
        }
        $this->assertGreaterThan(100, $nonEmptyKeys);

        $this->assertNotEmpty($theme->classes('padding', ['all' => 4]));
        $this->assertNotEmpty($theme->classes('sizedBox', ['width' => 4]));
        $this->assertNotEmpty($theme->classes('button', ['variant' => 'primary', 'size' => 'lg']));
        $this->assertNotEmpty($theme->classes('button', ['variant' => 'ghost', 'size' => 'sm']));
        $this->assertNotEmpty($theme->classes('alert', ['tone' => 'warning']));
        $this->assertNotEmpty($theme->classes('badge', ['color' => 'danger']));
    }
}
