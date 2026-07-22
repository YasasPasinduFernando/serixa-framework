<?php

declare(strict_types=1);

namespace Serixa\Tests\Styling;

use PHPUnit\Framework\TestCase;
use Serixa\Styling\CssOptimizer;
use Serixa\Styling\ThemeCompiler;

final class ThemeCompilerRuleMatrixTest extends TestCase
{
    public function testEveryCompiledRuleProducesCss(): void
    {
        $rules = (new ThemeCompiler())->compile();
        $this->assertGreaterThan(20, count($rules));

        foreach ($rules as $index => $rule) {
            $css = $rule->toCss();
            $this->assertNotSame('', $css, 'rule ' . $index);
            $this->assertStringContainsString('{', $css);
            $this->assertStringContainsString('}', $css);
            $this->assertNotSame('', $rule->selector()->value());
        }

        foreach ((new ThemeCompiler())->tokenNames() as $name) {
            $this->assertMatchesRegularExpression('/^[a-zA-Z0-9.]+$/', $name);
        }
    }

    public function testOptimizerPreservesSelectorCoverage(): void
    {
        $compiler = new ThemeCompiler();
        $raw = $compiler->compile();
        $optimized = (new CssOptimizer())->optimize($raw);

        $rawSelectors = [];
        foreach ($raw as $rule) {
            $rawSelectors[$rule->selector()->value()] = true;
        }
        $optSelectors = [];
        foreach ($optimized as $rule) {
            $optSelectors[$rule->selector()->value()] = true;
            $this->assertNotSame('', $rule->toCss());
        }

        foreach (array_keys($rawSelectors) as $selector) {
            $this->assertArrayHasKey($selector, $optSelectors, $selector);
        }
    }
}
