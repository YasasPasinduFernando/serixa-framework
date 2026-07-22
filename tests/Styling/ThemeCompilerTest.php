<?php

declare(strict_types=1);

namespace Serixa\Tests\Styling;

use PHPUnit\Framework\TestCase;
use Serixa\Styling\CssOptimizer;
use Serixa\Styling\StyleProperty;
use Serixa\Styling\StyleRule;
use Serixa\Styling\StyleSelector;
use Serixa\Styling\ThemeCompiler;

final class ThemeCompilerTest extends TestCase
{
    public function testCompilesTokenVariablesAndButtonRecipes(): void
    {
        $rules = (new ThemeCompiler())->compile();
        $css = '';
        foreach ($rules as $rule) {
            $css .= $rule->toCss();
        }

        $this->assertStringContainsString(':root{', $css);
        $this->assertStringContainsString('--sx-color-primary:', $css);
        $this->assertStringContainsString('.sx-btn-primary{', $css);
        $this->assertStringContainsString('background:var(--sx-color-primary)', $css);
    }

    public function testOptimizerMergesDuplicateSelectors(): void
    {
        $optimizer = new CssOptimizer();
        $merged = $optimizer->optimize([
            new StyleRule(StyleSelector::className('sx-btn'), [
                new StyleProperty('color', 'red'),
            ]),
            new StyleRule(StyleSelector::className('sx-btn'), [
                new StyleProperty('color', 'blue'),
                new StyleProperty('padding', '1rem'),
            ]),
        ]);

        $this->assertCount(1, $merged);
        $css = $merged[0]->toCss();
        $this->assertStringContainsString('color:blue', $css);
        $this->assertStringContainsString('padding:1rem', $css);
        $this->assertStringNotContainsString('color:red', $css);
    }
}
