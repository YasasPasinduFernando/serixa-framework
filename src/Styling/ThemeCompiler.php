<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * Compiles SERIXA design tokens and component recipes into StyleRule lists.
 *
 * Tokens become CSS custom properties on :root / .dark.
 * Components become semantic .sx-* classes (e.g. .sx-btn-primary).
 */
final class ThemeCompiler
{
    /** @var list<StyleRule>|null */
    private ?array $compiled = null;

    /**
     * @return list<StyleRule>
     */
    public function compile(): array
    {
        return $this->compiled ??= array_merge(
            $this->tokenRules(),
            $this->componentRecipes(),
        );
    }

    /**
     * Drop memoized rules (e.g. after swapping token sources in tests).
     */
    public function forget(): void
    {
        $this->compiled = null;
    }

    /**
     * @return list<string>
     */
    public function tokenNames(): array
    {
        return array_keys($this->tokenValues());
    }

    /**
     * CSS custom-property values for each design token (offline, no Tailwind).
     *
     * @return array<string, array{light: string, dark?: string}>
     */
    private function tokenValues(): array
    {
        return [
            'color.primary' => ['light' => '#0284c7', 'dark' => '#0ea5e9'],
            'color.primary.hover' => ['light' => '#0369a1', 'dark' => '#0284c7'],
            'color.secondary' => ['light' => '#f1f5f9', 'dark' => '#1e293b'],
            'color.secondary.hover' => ['light' => '#e2e8f0', 'dark' => '#334155'],
            'color.danger' => ['light' => '#dc2626', 'dark' => '#ef4444'],
            'color.danger.hover' => ['light' => '#b91c1c', 'dark' => '#dc2626'],
            'color.surface' => ['light' => '#ffffff', 'dark' => '#0f172a'],
            'color.background' => ['light' => '#f8fafc', 'dark' => '#020617'],
            'color.textPrimary' => ['light' => '#0f172a', 'dark' => '#f1f5f9'],
            'color.textMuted' => ['light' => '#64748b', 'dark' => '#94a3b8'],
            'color.onPrimary' => ['light' => '#ffffff', 'dark' => '#ffffff'],
            'spacing.small' => ['light' => '1rem'],
            'spacing.medium' => ['light' => '1.5rem'],
            'spacing.large' => ['light' => '2rem'],
            'radius.small' => ['light' => '0.25rem'],
            'radius.medium' => ['light' => '0.375rem'],
            'radius.large' => ['light' => '0.5rem'],
            'radius.full' => ['light' => '9999px'],
            'shadow.small' => ['light' => '0 1px 2px 0 rgb(0 0 0 / 0.05)'],
            'shadow.medium' => ['light' => '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)'],
            'shadow.large' => ['light' => '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)'],
            // Canonical names match TypographyToken; *.size kept as BC aliases.
            'typography.body' => ['light' => '1rem'],
            'typography.heading' => ['light' => '1.125rem'],
            'typography.label' => ['light' => '0.875rem'],
            'typography.body.size' => ['light' => '1rem'],
            'typography.heading.size' => ['light' => '1.125rem'],
            'typography.label.size' => ['light' => '0.875rem'],
            'typography.muted' => ['light' => '#64748b', 'dark' => '#94a3b8'],
            'border.subtle' => ['light' => '#e2e8f0', 'dark' => '#1e293b'],
            'border.strong' => ['light' => '#94a3b8', 'dark' => '#64748b'],
            'border.focus' => ['light' => '#0284c7', 'dark' => '#38bdf8'],
            'opacity.disabled' => ['light' => '0.5'],
            'opacity.muted' => ['light' => '0.7'],
            'opacity.overlay' => ['light' => '0.4'],
            'zIndex.base' => ['light' => '0'],
            'zIndex.dropdown' => ['light' => '40'],
            'zIndex.sticky' => ['light' => '50'],
            'zIndex.modal' => ['light' => '60'],
            'zIndex.toast' => ['light' => '70'],
            'breakpoint.sm' => ['light' => '640px'],
            'breakpoint.md' => ['light' => '768px'],
            'breakpoint.lg' => ['light' => '1024px'],
            'breakpoint.xl' => ['light' => '1280px'],
            'transition.fast' => ['light' => '100ms'],
            'transition.normal' => ['light' => '150ms'],
            'transition.slow' => ['light' => '300ms'],
        ];
    }

    /**
     * @return list<StyleRule>
     */
    private function tokenRules(): array
    {
        $light = [];
        $dark = [];

        foreach ($this->tokenValues() as $name => $values) {
            $var = $this->cssVarName($name);
            $light[] = new StyleProperty($var, $values['light']);
            if (isset($values['dark'])) {
                $dark[] = new StyleProperty($var, $values['dark']);
            }
        }

        $rules = [new StyleRule(StyleSelector::raw(':root'), $light)];
        if ($dark !== []) {
            $rules[] = new StyleRule(StyleSelector::raw('.dark'), $dark);
            $rules[] = new StyleRule(StyleSelector::raw('[data-theme="dark"]'), $dark);
        }

        return $rules;
    }

    /**
     * @return list<StyleRule>
     */
    private function componentRecipes(): array
    {
        $v = static fn (string $token): string => 'var(' . self::cssVarNameStatic($token) . ')';

        return [
            // Button base
            new StyleRule(StyleSelector::className('sx-btn'), [
                new StyleProperty('display', 'inline-flex'),
                new StyleProperty('align-items', 'center'),
                new StyleProperty('justify-content', 'center'),
                new StyleProperty('font-weight', '500'),
                new StyleProperty('border-radius', $v('radius.medium')),
                new StyleProperty('border', '0'),
                new StyleProperty('cursor', 'pointer'),
                new StyleProperty('transition', 'background-color ' . $v('transition.normal') . ' ease, color ' . $v('transition.normal') . ' ease, opacity ' . $v('transition.normal') . ' ease, box-shadow ' . $v('transition.fast') . ' ease'),
                new StyleProperty('text-decoration', 'none'),
            ]),
            new StyleRule(StyleSelector::raw('.sx-btn:focus-visible'), [
                new StyleProperty('outline', '2px solid ' . $v('border.focus')),
                new StyleProperty('outline-offset', '2px'),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-sm'), [
                new StyleProperty('font-size', '0.875rem'),
                new StyleProperty('padding', '0.375rem 0.75rem'),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-md'), [
                new StyleProperty('font-size', '0.875rem'),
                new StyleProperty('padding', '0.5rem 1rem'),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-lg'), [
                new StyleProperty('font-size', '1rem'),
                new StyleProperty('padding', '0.625rem 1.25rem'),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-primary'), [
                new StyleProperty('background', $v('color.primary')),
                new StyleProperty('color', $v('color.onPrimary')),
            ]),
            new StyleRule(StyleSelector::raw('.sx-btn-primary:hover'), [
                new StyleProperty('background', $v('color.primary.hover')),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-secondary'), [
                new StyleProperty('background', $v('color.secondary')),
                new StyleProperty('color', $v('color.textPrimary')),
            ]),
            new StyleRule(StyleSelector::raw('.sx-btn-secondary:hover'), [
                new StyleProperty('background', $v('color.secondary.hover')),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-danger'), [
                new StyleProperty('background', $v('color.danger')),
                new StyleProperty('color', $v('color.onPrimary')),
            ]),
            new StyleRule(StyleSelector::raw('.sx-btn-danger:hover'), [
                new StyleProperty('background', $v('color.danger.hover')),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-ghost'), [
                new StyleProperty('background', 'transparent'),
                new StyleProperty('color', $v('color.textPrimary')),
            ]),
            new StyleRule(StyleSelector::raw('.sx-btn-ghost:hover'), [
                new StyleProperty('background', $v('color.secondary')),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-rounded'), [
                new StyleProperty('border-radius', $v('radius.full')),
            ]),
            new StyleRule(StyleSelector::className('sx-btn-disabled'), [
                new StyleProperty('opacity', $v('opacity.disabled')),
                new StyleProperty('cursor', 'not-allowed'),
                new StyleProperty('pointer-events', 'none'),
            ]),

            // Card
            new StyleRule(StyleSelector::className('sx-card'), [
                new StyleProperty('background', $v('color.surface')),
                new StyleProperty('color', $v('color.textPrimary')),
                new StyleProperty('border-radius', $v('radius.large')),
                new StyleProperty('box-shadow', $v('shadow.small')),
                new StyleProperty('border', '1px solid ' . $v('border.subtle')),
            ]),

            // Form control
            new StyleRule(StyleSelector::className('sx-input'), [
                new StyleProperty('display', 'block'),
                new StyleProperty('width', '100%'),
                new StyleProperty('border-radius', $v('radius.medium')),
                new StyleProperty('border', '1px solid ' . $v('border.strong')),
                new StyleProperty('background', $v('color.surface')),
                new StyleProperty('color', $v('color.textPrimary')),
                new StyleProperty('padding', '0.5rem 0.75rem'),
                new StyleProperty('font-size', '0.875rem'),
            ]),
            new StyleRule(StyleSelector::raw('.sx-input:focus-visible'), [
                new StyleProperty('outline', '2px solid ' . $v('border.focus')),
                new StyleProperty('outline-offset', '1px'),
            ]),
            new StyleRule(StyleSelector::className('sx-label'), [
                new StyleProperty('display', 'block'),
                new StyleProperty('font-size', $v('typography.label.size')),
                new StyleProperty('font-weight', '500'),
                new StyleProperty('color', $v('color.textPrimary')),
                new StyleProperty('margin-bottom', '0.25rem'),
            ]),

            // Typography helpers
            new StyleRule(StyleSelector::className('sx-text'), [
                new StyleProperty('color', $v('color.textPrimary')),
                new StyleProperty('font-size', $v('typography.body.size')),
            ]),
            new StyleRule(StyleSelector::className('sx-text-muted'), [
                new StyleProperty('color', $v('typography.muted')),
            ]),
            new StyleRule(StyleSelector::className('sx-heading'), [
                new StyleProperty('color', $v('color.textPrimary')),
                new StyleProperty('font-size', $v('typography.heading.size')),
                new StyleProperty('font-weight', '600'),
            ]),
        ];
    }

    private function cssVarName(string $token): string
    {
        return self::cssVarNameStatic($token);
    }

    private static function cssVarNameStatic(string $token): string
    {
        return '--sx-' . str_replace('.', '-', $token);
    }
}
