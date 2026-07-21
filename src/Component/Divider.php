<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Semantic rule. Supports horizontal/vertical orientation and spacing.
 *
 * @example Divider::make()->orientation('vertical')->spacing('sm')
 */
final class Divider extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $orientation = 'horizontal';

    private string $spacing = 'md';

    private ?string $label = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function orientation(string $orientation): self
    {
        $this->assertOption($orientation, ['horizontal', 'vertical'], 'orientation');
        $this->orientation = $orientation;

        return $this;
    }

    public function horizontal(): self
    {
        return $this->orientation('horizontal');
    }

    public function vertical(): self
    {
        return $this->orientation('vertical');
    }

    public function spacing(string $spacing): self
    {
        $this->assertOption($spacing, ['none', 'sm', 'md', 'lg'], 'spacing');
        $this->spacing = $spacing;

        return $this;
    }

    /**
     * Optional accessible label (aria-label on the rule).
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('divider', [
                'orientation' => $this->orientation,
                'spacing' => $this->spacing,
            ]),
            [
                'role' => 'separator',
                'aria-orientation' => $this->orientation,
                'aria-label' => $this->label,
            ],
        );

        return sprintf('<hr%s />', $attrs);
    }
}
