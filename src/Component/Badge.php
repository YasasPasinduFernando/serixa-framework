<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Status badge (static / PE-ready).
 *
 * @example Badge::make('Admin')->color('primary')
 */
final class Badge extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $label;

    private string $color = 'neutral';

    private function __construct(string $label)
    {
        parent::__construct();
        $this->label = $label;
    }

    public static function make(string $label): self
    {
        return new self($label);
    }

    public function color(string $color): self
    {
        $this->assertOption($color, ['neutral', 'primary', 'success', 'warning', 'danger'], 'color');
        $this->color = $color;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('badge', ['color' => $this->color]),
        );

        return sprintf('<span%s>%s</span>', $attrs, $this->escape($this->label));
    }
}
